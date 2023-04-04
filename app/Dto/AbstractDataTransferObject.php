<?php

namespace App\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\AssignOp\Mod;

/**
 * @note
 * Abstract data transfer object for Laravel
 */
abstract class AbstractDataTransferObject implements DTOInterface
{
    const JAVASCRIPT_UNDERSCORE = '_';
    private bool $_isDataFromHTTPRequest = false;
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

    private function setOriginalModel(Model $data): void
    {
        $this->original_model_obj = $data;
    }

    public function setDataFrom(Model|array|Request $target): static
    {
        // TODO: Implement setDataFrom() method.
    }

    public function getDataArrayFrom(Model|array|Request $data): array
    {
        // TODO: Implement getDataArrayFrom() method.
    }

    public function getOriginalModel(): Model|null
    {
        // TODO: Implement getOriginalModel() method.
    }

    public function toArrayForModel(): array
    {
        // TODO: Implement toArrayForModel() method.
    }

    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }

    public function toJson(): string
    {
        // TODO: Implement toJson() method.
    }



}
