<?php

namespace d2gPmPluginCarPartsFinder\Contracts;

use d2gPmPluginCarPartsFinder\Models\Car;

interface CarRepositoryContract
{
    /**
     * Search for car by name or create a new entry
     *
     * @param int $platformId
     * @param array $data
     * @return Car
     */
    public function firstOrCreate($platformId, array $data): Car;

    /**
     * Add a new car
     *
     * @param int $platformId
     * @param array $data
     * @return Car
     */
    public function createCar($platformId, array $data): Car;

    /**
     * List all cars
     *
     * @param int $platformId
     * @return Car[]
     */
    public function getCarList($platformId): array;


    /**
     * Get a car
     *
     * @param int $id
     * @return Car
     */
    public function getCar($id): Car;

    /**
     * Update the car
     *
     * @param int $id
     * @param array $data
     * @return Car
     */
    public function updateCar($id, array $data): Car;

    /**
     * Delete a car
     *
     * @param int $id
     * @return Car
     */
    public function deleteCar($id): Car;
}
