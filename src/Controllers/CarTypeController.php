<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\CarBrandRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarTypeRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

class CarTypeController extends Controller
{

    /**
     * @var CarTypeRepositoryContract
     */
    private $carTypeRepositoryContract;

    /**
     * @param CarTypeRepositoryContract $carTypeRepositoryContract
     */
    public function __construct(CarTypeRepositoryContract $carTypeRepositoryContract)
    {
        $this->carTypeRepositoryContract = $carTypeRepositoryContract;
        parent::__construct();
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param Request $r
     * @return string
     */
    public function index(int $brandId, int $modelId, Request $r): string
    {
        $page = intval($r->get('page', 1));
        $itemsPerPage = intval($r->get('itemsPerPage', 1));

        $result = [
            'totalsCount' => 0,
            'isLastPage' => true,
            'lastPageNumber' => 1,
            'firstOnPage' => ($itemsPerPage*$page)-$itemsPerPage+1,
            'lastOnPage' => $itemsPerPage*$page,
            'page' => $page,
            'entries' => [],
            'itemsPerPage' => $itemsPerPage
        ];

        $types = $this->carTypeRepositoryContract->getCarTypeList($modelId);

        $result['totalsCount'] = count($types);

        $types = array_slice($types, $result['itemsPerPage']*($result['page']-1), $result['itemsPerPage']);

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];
        $result['entries'] = $types;

        return json_encode($result);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param Request $request
     * @return string
     */
    public function store(Request $request, int $brandId, int $modelId): string
    {
        $store = $this->carTypeRepositoryContract->createCarType($modelId, $request->all());

        return json_encode($store);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $id
     * @return string
     */
    public function get(int $brandId, int $modelId, int $id): string
    {
        $get = $this->carTypeRepositoryContract->getCarType($id);

        return json_encode($get);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $id
     * @param Request $request
     * @return string
     */
    public function update(Request $request, int $brandId, int $modelId, int $id): string
    {
        $update = $this->carTypeRepositoryContract->updateCarType($id, $request->all());
        return json_encode($update);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $id
     * @return string
     */
    public function delete(int $brandId, int $modelId, int $id): string
    {
        $delete = $this->carTypeRepositoryContract->deleteCarType($id);
        return json_encode($delete);
    }
}
