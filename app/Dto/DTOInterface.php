<?php

namespace App\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface DTOInterface
{
    /**
     * Create an empty DTO Object.
     *
     * Use this method when need the empty DTO object in order to Save the data
     *
     * @return static
     */
    public static function make(): static;

    /**
     * Create new DTO object with given data
     *
     * Use this method changing the data from Model, array or Request to DTO Object.
     * Only
     *
     * @param Model|Request|array $data
     *
     * @return static
     */
    public static function makeWith(Model|Request|array $data): static;

    /**
     * Just set the Data from Model, Array, Request to DTO object already exist.
     *
     * If there is DTO object which is already made, set the data to that DTO object.
     *
     * @param Model|Request|array $target
     * @return static
     */
    public function setDataFrom(Model|Request|array $target): static;

    /**
     * Get original Model object.
     *
     * When use makeWith() method, if $data is the Model object then DTO hold the original Model
     * to use later; e.g. making relation with other model directly.
     *
     * @return Model|null
     */
    public function getOriginalModel(): Model|null;

    /**
     * Get only Scalar data for creating Model object
     *
     * Data Transfer Object can hold the relational data with Array or other DTO object.
     * (e.g. User DTO can hold the related Role DTO data.)
     * In order to change the DTO data to array format to insert to the Model,
     * I ignored any other relational data like array or object.
     *
     * The Model's fillable will distill the data, which is only acceptable.
     */
    public function toArrayForModel(): array;

    /**
     * Get all the data with Array format.
     *
     * This method return not only Scalar data but also other array and object data
     * in which DTO hold.
     *
     * @return array
     */
    public function toArray(): array;

    public function toJson():string;
}
