<?php

namespace App\Dto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface DTOInterface
{
    public static function make(): static;
    public static function makeWith(Model|Request|array $data): static;
    public function setDataFrom(Model|Request|array $target): static;
    public function getDataArrayFrom(Model|Request|array $data):array;
    public function getOriginalModel(): Model|null;
    public function toArrayForModel(): array;
    public function toArray(): array;
    public function toJson():string;
}
