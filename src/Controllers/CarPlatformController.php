<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\CarPlatformRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

class CarPlatformController extends Controller
{

    /**
     * @var CarPlatformRepositoryContract
     */
    private $carPlatformRepositoryContract;

    /**
     * @param CarPlatformRepositoryContract $carPlatformRepositoryContract
     */
    public function __construct(CarPlatformRepositoryContract $carPlatformRepositoryContract)
    {
        $this->carPlatformRepositoryContract = $carPlatformRepositoryContract;
        parent::__construct();
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param Request $r
     * @return string
     */
    public function index(int $brandId, int $modelId, int $typeId, Request $r): string
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

        $platforms = $this->carPlatformRepositoryContract->getCarPlatformList($typeId);

        $result['totalsCount'] = count($platforms);

        $platforms = array_slice($platforms, $result['itemsPerPage']*($result['page']-1), $result['itemsPerPage']);

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];
        $result['entries'] = $platforms;

        return json_encode($result);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param Request $request
     * @return string
     */
    public function store(Request $request, int $brandId, int $modelId, int $typeId): string
    {
        $store = $this->carPlatformRepositoryContract->createCarPlatform($typeId, $request->all());

        return json_encode($store);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $id
     * @return string
     */
    public function get(int $brandId, int $modelId, int $typeId, int $id): string
    {
        $get = $this->carPlatformRepositoryContract->getCarPlatform($id);

        return json_encode($get);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $id
     * @param Request $request
     * @return string
     */
    public function update(int $brandId, int $modelId, int $typeId, int $id, Request $request): string
    {
        $update = $this->carPlatformRepositoryContract->updateCarPlatform($id, $request->all());
        return json_encode($update);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $id
     * @return string
     */
    public function delete(int $brandId, int $modelId, int $typeId, int $id): string
    {
        $delete = $this->carPlatformRepositoryContract->deleteCarPlatform($id);
        return json_encode($delete);
    }
}
