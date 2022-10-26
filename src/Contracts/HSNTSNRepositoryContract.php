<?php

namespace d2gPmPluginCarPartsFinder\Contracts;

use d2gPmPluginCarPartsFinder\Models\HSNTSN;

interface HSNTSNRepositoryContract
{
    /**
     * Search for hsn/tsn by name or create a new entry
     *
     * @param int $carId
     * @param array $data
     * @return HSNTSN
     */
    public function firstOrCreate($carId, array $data): HSNTSN;

    /**
     * Add a new hsn/tsn
     *
     * @param int $carId
     * @param array $data
     * @return HSNTSN
     */
    public function create($carId, array $data): HSNTSN;

    /**
     * List all hsn/tsn
     *
     * @param int $carId
     * @return HSNTSN[]
     */
    public function index($carId): array;


    /**
     * Get a hsn/tsn
     *
     * @param int $id
     * @return HSNTSN
     */
    public function get($id): HSNTSN;

    /**
     * Update the hsn/tsn
     *
     * @param int $id
     * @param array $data
     * @return HSNTSN
     */
    public function update($id, array $data): HSNTSN;

    /**
     * Delete a hsn/tsn
     *
     * @param int $id
     * @return HSNTSN
     */
    public function delete($id): HSNTSN;
}
