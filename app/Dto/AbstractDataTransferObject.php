<?php

namespace App\Dto;

use Error;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;

/**
 * @note
 * Abstract data transfer object for Laravel
 */
abstract class AbstractDataTransferObject implements DTOInterface
{
    const JAVASCRIPT_UNDERSCORE = '_';
    private bool $isDataFromHTTPRequest = false;
    public Model|null $original_model_obj = null;

    public static function make(): static
    {
        return new static();
    }

    public static function makeWith(Model|array|Request $data): static
    {
        $dto = self::make()->setDataFrom($data);

        if ($data instanceof Model) {
            $dto->setOriginalModel($data);
        }

        return $dto;
    }

    public function setDataFrom(Model|array|Request $target): static
    {
        $this->checkDataOrigin($target);
        $dataList = self::getDataArrayFrom($target);

        return $this->setDataFromArray($dataList);
    }

    private function getDataArrayFrom(Model|array|Request $data): array
    {
        if (is_array($data)) {
            $arrayData = $data;
        } else {
            $arrayData = $data->toArray();
        }

        return $arrayData;
    }

    public function getOriginalModel(): Model|null
    {
        return $this->original_model_obj;
    }

    public function toArrayForModel(): array
    {
        $valueList = $this->getOnlyScalarTypeValueList();
        return $this->filteringByProperVariables($valueList);
    }

    public function toArray(): array
    {
        return $this->removeNullValueFromArray($this->getPrivateVariableValueList());
    }

    public function toJson(): string
    {
        return json_encode($this->getPrivateVariableValueList());
    }

    private function setOriginalModel(Model $data): void
    {
        $this->original_model_obj = $data;
    }

    private function checkDataOrigin(Model|Request|array $target): void
    {
        if ($target instanceof Request) {
            $this->isDataFromHTTPRequest = true;
        }
    }

    private function setDataFromArray(array $dataList): static
    {
        $voVariableList = $this->filteringByProperVariables($this->getPrivateVariableList());

        foreach ($voVariableList as $variableName => $defaultValue) {
            $value = $this->getValueFromDataList($dataList, $variableName);

            if ($this->isNotNull($value)) {
                $this->executeSetMethodWith($variableName, $value);
            }
        }
        return $this;
    }

    /**
     * Get private variable name list from DTO Object for further usage.
     *
     * I recommend to use the getter, setter patterns in the DTO object
     * since data transfer object can secure data with only accepting setter or getter method not directly change.
     * I set the variable name as a key of array then further method will set the value depends on the variable name.
     *
     * @return array<string, null>;
     */
    private function getPrivateVariableList(): array
    {
        $privateVariableList = [];
        $reflect = new ReflectionClass(new static());
        $properties = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $privateVariableList[$property->name] = null;
        }

        return $privateVariableList;
    }

    /**
     * @param array<string, null> $valueList
     * @return array<string, null>
     */
    private function filteringByProperVariables(array $valueList): array
    {
        return array_filter($valueList, function ($key) {
            return $this->isProperVariableForModel($key);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Check the data from JavaScript object which use the underscore variables.
     *
     * This method validate that data from JavaScript object which consist public variable.
     * If JavaScript object hold the private underscore variable, in this case we skip that variables.
     * So to send the data from the front end javascript object, it is better to change
     * private variables to public variables.
     *
     * @param string $variableName
     * @return bool
     */
    private function isProperVariableForModel(string $variableName): bool
    {
        $delimiter = substr($variableName, 0, 1);

        if ($delimiter === $this::JAVASCRIPT_UNDERSCORE) {
            return false;
        }

        return true;
    }

    private function getValueFromDataList(array $dataList, string $variableName): mixed
    {
        $value = Arr::get($dataList, $variableName);

        if ($this->isNeedToCheckUnderscoreVariableName($value)) {
            $underscoreVariableName = $this::JAVASCRIPT_UNDERSCORE . $variableName;
            $value = Arr::get($dataList, $underscoreVariableName);
        }

        return $value;
    }

    private function isNeedToCheckUnderscoreVariableName(mixed $value): bool
    {
        return $this->isDataFromHTTPRequest && is_null($value);
    }

    private function executeSetMethodWith(string $variableName, mixed $value): void
    {
        $setMethodName = $this->combineSetMethodName($variableName);
        if (method_exists($this, $setMethodName)) {
            call_user_func([$this, $setMethodName], $value);
        }
    }

    private function getOnlyScalarTypeValueList(): array
    {
        $variableList = $this->getPrivateVariableValueList();
        return array_filter($variableList, function ($value) {
            return is_scalar($value);
        });
    }

    private function getPrivateVariableValueList(): array
    {
        $privateVariableValueList = [];
        foreach ($this->getPrivateVariableList() as $variableName => $value) {
            $privateVariableValueList[$variableName] = $this->getPropertyValue($variableName);
        }
        return $privateVariableValueList;
    }

    private function getPropertyValue(string $name): mixed
    {
        $value = null;
        $getMethodName = $this->combineGetMethodName($name);
        $booleanGetMethodName = $this->combineBooleanGetMethodName($name);

        if (method_exists($this, $getMethodName)) {
            $value = $this->callMethod($getMethodName);
        } elseif (method_exists($this, $booleanGetMethodName)) {
            $value = $this->callMethod($booleanGetMethodName);
        }

        return $value;
    }

    private function removeNullValueFromArray(array $variableList): array
    {
        $removedNullValueList = [];

        foreach ($variableList as $variableName => $value) {
            if ($this->isNotNull($value)) {
                switch (gettype($value)) {
                    case 'object':
                        $removedNullValueList[$variableName] = $this->getValueListWhenObjectType($value);
                        break;
                    case 'array':
                        if ($this->hasValueInArray($value)) {
                            $removedNullValueList[$variableName] = $this->removeNullValueFromArray($value);
                        }
                        break;
                    default:
                        $removedNullValueList[$variableName] = $value;
                        break;
                }
            }
        }

        return $removedNullValueList;
    }

    private function getArrayAfterRemovingNullValue(object $target): array
    {
        $variableList = get_object_vars($target);
        return $this->removeNullValueFromArray($variableList);
    }

    private function getValueListWhenObjectType(object $value): array
    {
        if ($value instanceof DTOInterface) {
            $valueListAfterRemovedNullValue = $this->removeNullValueFromArray($value->toArray());
        } else {
            $valueListAfterRemovedNullValue = $this->getArrayAfterRemovingNullValue($value);
        }

        return $valueListAfterRemovedNullValue;
    }

    private function combineSetMethodName(string $variableName): string
    {
        return 'set' . ucfirst(Str::camel($variableName));
    }

    private function combineGetMethodName(string $variableName): string
    {
        return 'get' . ucfirst(Str::camel($variableName));
    }

    private function combineBooleanGetMethodName(string $variableName): string
    {
        return 'is' . ucfirst(Str::camel($variableName));
    }

    private function callMethod(string $methodName): mixed
    {
        $value = null;
        try {
            $value = call_user_func([$this, $methodName]);
        } catch (Exception|Error $e) {
            /**
             * This is the noticeable Exception or error, so I ignore the Exception or Error's.
             * The developer can know easily that setting data failed because DTO didn't hold the data.
             */
        }
        return $value;
    }

    private function isNotNull(mixed $value): bool
    {
        return !is_null($value);
    }

    private function hasValueInArray(array $value): bool
    {
        return !empty($value);
    }
}
