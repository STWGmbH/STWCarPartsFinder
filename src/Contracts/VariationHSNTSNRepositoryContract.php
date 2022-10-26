<?php

namespace d2gPmPluginCarPartsFinder\Contracts;

use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use Plenty\Exceptions\ValidationException;

interface VariationHSNTSNRepositoryContract
{

    /**
     * Add a new link
     *
     * @param int $variationId
     * @param array $hsntsnList

     */
    public function createBatch(int $variationId, array $hsntsnList);

    /**
     * Add a new link
     *
     * @param int $hsntsnId
     * @param array $data
     * @throws ValidationException

     */
    public function create(int $hsntsnId, array $data): array;

    /**
     * List all links
     *
     * @param int $hsntsnId
     * @return VariationHSNTSN[]
     */
    public function index(int $hsntsnId): array;

    /**
     * List variation ids by car
     *
     * @param int $hsntsnId
     * @param int $categoryId
     * @return array
     */
    public function indexByCategory(int $hsntsnId, int $categoryId): array;


    /**
     * Delete a link
     *
     * @param array $data
     * @return bool
     */
    public function delete(array $data):bool;
}
