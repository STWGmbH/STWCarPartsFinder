<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\VariationCarRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\VariationHSNTSNRepositoryContract;
use Plenty\Exceptions\ValidationException;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

class VariationHSNTSNController extends Controller
{

    /**
     * @var VariationHSNTSNRepositoryContract
     */
    private $variationHSNTSNRepositoryContract;

    /**
     * @param VariationHSNTSNRepositoryContract $variationHSNTSNRepositoryContract
     */
    public function __construct(VariationHSNTSNRepositoryContract $variationHSNTSNRepositoryContract)
    {
        $this->variationHSNTSNRepositoryContract = $variationHSNTSNRepositoryContract;
        parent::__construct();
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @param int $hsntsnId
     * @return string
     */
    public function index(int $brandId, int $modelId, int $typeId, int $platformId, int $carId, int $hsntsnId): string
    {
        $index = $this->variationHSNTSNRepositoryContract->index($hsntsnId);

        return json_encode($index);
    }

    /**
     * @param Request $request
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @param int $hsntsnId
     * @return string
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $store = [];

        foreach($request->get('hsntsnSet') as $hsntsnId) {
            $store[] = $this->variationHSNTSNRepositoryContract->create(intval($hsntsnId), $request->all());
        }

        return json_encode($store);
    }

    /**
     * @param Request $request
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @param int $hsntsnId
     * @return string
     */
    public function delete(Request $request)
    {
        $delete = $this->variationHSNTSNRepositoryContract->delete($request->all());
        return json_encode($delete);
    }
}
