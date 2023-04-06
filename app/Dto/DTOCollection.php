<?php

namespace App\Dto;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use RuntimeException;

class DTOCollection
{
    private Collection $collection;
    private string $className;

    public function __construct(Collection $collection, string $className)
    {
        try {
            $this->validateClassType($className);
            $this->collection = $collection;
            $this->className = $className;
        } catch (BindingResolutionException $e) {
            throw new RuntimeException($e);
        }
    }

    public static function getInstance(Collection $collection, string $className): DTOCollection
    {
        return new DTOCollection($collection, $className);
    }

    public static function fromJson(string $jsonString, string $className): DTOCollection
    {
        $dataList = json_decode($jsonString, true);
        return new DTOCollection(collect($dataList), $className);
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return DTOInterface[]
     */
    public function getDTOList(): array
    {
        $dtoList = [];

        foreach ($this->collection as $obj) {
            $dto = $this->getNewDTOObj();
            $dtoList[] = $dto->setDataFrom($obj);
        }

        return $dtoList;
    }

    public function toJson(): string
    {
        $dtoList = [];

        foreach ($this->collection as $obj) {
            $dto = $this->getNewDTOObj();
            $dtoList[] = $dto->setDataFrom($obj)->toArray();
        }

        return json_encode($dtoList);
    }

    /**
     * Get only id list from DTO object list if DTO has the id variable
     *
     * @return int[]
     */
    public function getIdList(): array
    {
        $idList = [];

        foreach ($this->collection as $obj) {
            $dto = $this->getNewDTOObj()->setDataFrom($obj);
            if (method_exists($dto, 'getId')) {
                $idList[] = $dto->getId();
            }
        }

        return $idList;
    }

    /**
     * If type is not instance of DTOInterface then occur the Exception.
     *
     * @throw RuntimeException
     */
    private function validateClassType(string $className): void
    {
        try {
            $obj = new $className();
            if ($this->isNotDTOInstance($obj)) {
                /**
                 * When just throw Exception here, catch() phrase catches the exception right way.
                 */
                throw new RuntimeException();
            }
        } catch (Exception $e) {
            debug_backtrace();
            throw new RuntimeException(DTOInterface::class.
                ' is a expected type, but the argument is not matched. Your argument is '.$className);
        } finally {
            unset($obj);
        }
    }

    private function getNewDTOObj(): DTOInterface
    {
        return new $this->className();
    }

    private function isNotDTOInstance(mixed $obj): bool
    {
        return !($obj instanceof DTOInterface);
    }
}
