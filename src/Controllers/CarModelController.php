<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\CarModelRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

class CarModelController extends Controller
{

    /**
     * @var CarModelRepositoryContract
     */
    private $carModelRepositoryContract;

    /**
     * @param CarModelRepositoryContract $carModelRepositoryContract
     */
    public function __construct(CarModelRepositoryContract $carModelRepositoryContract)
    {
        $this->carModelRepositoryContract = $carModelRepositoryContract;
        parent::__construct();
    }

    /**
     * @param int $brandId
     * @param Request $r
     * @return string
     */
    public function index(Request $r): string
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

        $models = $this->carModelRepositoryContract->getCarModelList($r->get('id'));

        $result['totalsCount'] = count($models);

        $models = array_slice($models, $result['itemsPerPage']*($result['page']-1), $result['itemsPerPage']);

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];
        $result['entries'] = $models;

        return json_encode($result);
    }

    /**
     * @param Request $request
     * @param int $brandId
     * @return string
     */
    public function store(Request $request, int $brandId): string
    {
        $store = $this->carModelRepositoryContract->createCarModel($brandId, $request->all());

        return json_encode($store);
    }

    /**
     * @param int $id
     * @param int $brandId
     * @return string
     */
    public function get(int $brandId, $id): string
    {
        $get = $this->carModelRepositoryContract->getCarModel($id);

        return json_encode($get);
    }

    /**
     * @param Request $request
     * @param int $brandId
     * @param int $id
     * @return string
     */
    public function update(Request $request, int $brandId, int $id): string
    {
        $update = $this->carModelRepositoryContract->updateCarModel($id, $request->all());
        return json_encode($update);
    }

    /**
     * @param int $brandId
     * @param int $id
     * @return string
     */
    public function delete(int $brandId, int $id): string
    {
        $delete = $this->carModelRepositoryContract->deleteCarModel($id);
        return json_encode($delete);
    }
}
