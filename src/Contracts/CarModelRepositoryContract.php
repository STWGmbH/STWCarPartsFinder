<?php

namespace d2gPmPluginCarPartsFinder\Contracts;

use d2gPmPluginCarPartsFinder\Models\CarModel;

interface CarModelRepositoryContract
{
    /**
     * Search for model by name or create a new entry
     *
     * @param int $brandId
     * @param array $data
     * @return CarModel
     */
    public function firstOrCreate($brandId, array $data): CarModel;

    /**
     * Add a new brand
     *
     * @param int $brandId
     * @param array $data
     * @return CarModel
     */
    public function createCarModel($brandId, array $data): CarModel;

    /**
     * List all brands
     *
     * @param int $brandId
     * @return CarModel[]
     */
    public function getCarModelList($brandId): array;


    /**
     * Get a brands
     *
     * @param int $id
     * @return CarModel
     */
    public function getCarModel($id): CarModel;

    /**
     * Update the brand
     *
     * @param int $id
     * @param array $data
     * @return CarModel
     */
    public function updateCarModel($id, array $data): CarModel;

    /**
     * Delete a brand
     *
     * @param int $id
     * @return CarModel
     */
    public function deleteCarModel($id): CarModel;
}
