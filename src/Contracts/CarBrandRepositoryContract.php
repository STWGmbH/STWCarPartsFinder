<?php

namespace d2gPmPluginCarPartsFinder\Contracts;

use d2gPmPluginCarPartsFinder\Models\CarBrand;

interface CarBrandRepositoryContract
{

    /**
     * Search for brand by name or create a new entry
     *
     * @param array $data
     * @return CarBrand
     */
    public function firstOrCreate(array $data): CarBrand;

    /**
     * Add a new brand
     *
     * @param array $data
     * @return CarBrand
     */
    public function createCarBrand(array $data): CarBrand;

    /**
     * List all brands
     *
     * @return CarBrand[]
     */
    public function getCarBrandList(): array;

    /**
     * Get a brands
     *
     * @param int $id
     * @return CarBrand
     */
    public function getCarBrand($id): CarBrand;

    /**
     * Update the brand
     *
     * @param int $id
     * @param array $data
     * @return CarBrand
     */
    public function updateCarBrand($id, array $data): CarBrand;

    /**
     * Delete a brand
     *
     * @param int $id
     * @return CarBrand
     */
    public function deleteCarBrand($id): CarBrand;
}
