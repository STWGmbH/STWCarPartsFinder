<?php

namespace d2gPmPluginCarPartsFinder\Contracts;

use d2gPmPluginCarPartsFinder\Models\CarType;

interface CarTypeRepositoryContract
{
    /**
     * Search for type by name or create a new entry
     *
     * @param int $modelId
     * @param array $data
     * @return CarType
     */
    public function firstOrCreate($modelId, array $data): CarType;

    /**
     * Add a new type
     *
     * @param int $modelId
     * @param array $data
     * @return CarType
     */
    public function createCarType($modelId, array $data): CarType;

    /**
     * List all types
     *
     * @param int $modelId
     * @return CarType[]
     */
    public function getCarTypeList($modelId): array;


    /**
     * Get a type
     *
     * @param int $id
     * @return CarType
     */
    public function getCarType($id): CarType;

    /**
     * Update the type
     *
     * @param int $id
     * @param array $data
     * @return CarType
     */
    public function updateCarType($id, array $data): CarType;

    /**
     * Delete a type
     *
     * @param int $id
     * @return CarType
     */
    public function deleteCarType($id): CarType;
}
