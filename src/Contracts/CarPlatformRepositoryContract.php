<?php

namespace d2gPmPluginCarPartsFinder\Contracts;

use d2gPmPluginCarPartsFinder\Models\CarPlatform;

interface CarPlatformRepositoryContract
{
    /**
     * Search for platform by name or create a new entry
     *
     * @param int $typeId
     * @param array $data
     * @return CarPlatform
     */
    public function firstOrCreate($typeId, array $data): CarPlatform;

    /**
     * Add a new platform
     *
     * @param int $typeId
     * @param array $data
     * @return CarPlatform
     */
    public function createCarPlatform($typeId, array $data): CarPlatform;

    /**
     * List all platforms
     *
     * @param int $typeId
     * @return CarPlatform[]
     */
    public function getCarPlatformList($typeId): array;


    /**
     * Get a platform
     *
     * @param int $id
     * @return CarPlatform
     */
    public function getCarPlatform($id): CarPlatform;

    /**
     * Update the platform
     *
     * @param int $id
     * @param array $data
     * @return CarPlatform
     */
    public function updateCarPlatform($id, array $data): CarPlatform;

    /**
     * Delete a platform
     *
     * @param int $id
     * @return CarPlatform
     */
    public function deleteCarPlatform($id): CarPlatform;
}
