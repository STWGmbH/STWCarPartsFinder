<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\CarRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

class CarController extends Controller
{

    /**
     * @var CarRepositoryContract
     */
    private $carRepositoryContract;

    /**
     * @param CarRepositoryContract $carRepositoryContract
     */
    public function __construct(CarRepositoryContract $carRepositoryContract)
    {
        $this->carRepositoryContract = $carRepositoryContract;
        parent::__construct();
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param Request $r
     * @return string
     */
    public function index(int $brandId, int $modelId, int $typeId, int $platformId, Request $r): string
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

        $cars = $this->carRepositoryContract->getCarList($platformId);

        $result['totalsCount'] = count($cars);

        $cars = array_slice($cars, $result['itemsPerPage']*($result['page']-1), $result['itemsPerPage']);

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];
        $result['entries'] = $cars;

        return json_encode($result);
    }

    /**
     * @param Request $request
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @return string
     */
    public function store(Request $request, int $brandId, int $modelId, int $typeId, int $platformId): string
    {
        $store = $this->carRepositoryContract->createCar($platformId, $request->all());

        return json_encode($store);
    }

    /**
     * @param int $id
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @return string
     */
    public function get(int $brandId, int $modelId, int $typeId, int $platformId, int $id): string
    {
        $get = $this->carRepositoryContract->getCar($id);

        return json_encode($get);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @return string
     */
    public function update(Request $request, int $brandId, int $modelId, int $typeId, int $platformId, int $id): string
    {
        $update = $this->carRepositoryContract->updateCar($id, $request->all());
        return json_encode($update);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $id
     * @return string
     */
    public function delete(int $brandId, int $modelId, int $typeId, int $platformId, int $id): string
    {
        $delete = $this->carRepositoryContract->deleteCar($id);
        return json_encode($delete);
    }
}
