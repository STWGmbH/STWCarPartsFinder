<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\CarBrandRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

class CarBrandController extends Controller
{

    /**
     * @var CarBrandRepositoryContract
     */
    private $carBrandRepositoryContract;

    /**
     * @param CarBrandRepositoryContract $carBrandRepositoryContract
     */
    public function __construct(CarBrandRepositoryContract $carBrandRepositoryContract)
    {
        $this->carBrandRepositoryContract = $carBrandRepositoryContract;
        parent::__construct();
    }

    /**
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

        $brands = $this->carBrandRepositoryContract->getCarBrandList();

        if($r->has('id') || $r->has('name')) {
            $brandsFilter = [];
            foreach ($brands as $brand) {
                if($brand->id == $r->get('id') || strtolower($brand->name) == strtolower($r->get('name'))){
                    array_push($brandsFilter, $brand);
                }
            }
            $brands = $brandsFilter;
        }

        $result['totalsCount'] = count($brands);

        $brands = array_slice($brands, $result['itemsPerPage']*($result['page']-1), $result['itemsPerPage']);

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];
        $result['entries'] = $brands;

        return json_encode($result);
    }

    /**
     * @param  \Plenty\Plugin\Http\Request $request
     * @return string
     */
    public function store(Request $request): string
    {
        $store = $this->carBrandRepositoryContract->createCarBrand($request->all());

        return json_encode($store);
    }


    /**
     * @param int $id
     * @return string
     */
    public function get($id): string
    {
        $get = $this->carBrandRepositoryContract->getCarBrand($id);

        return json_encode($get);
    }

    /**
     * @param int $id
     * @param  \Plenty\Plugin\Http\Request $request
     * @return string
     */
    public function update(int $id, Request $request): string
    {
        $update = $this->carBrandRepositoryContract->updateCarBrand($id, $request->all());
        return json_encode($update);
    }

    /**
     * @param int $id
     * @return string
     */
    public function delete(int $id): string
    {
        $delete = $this->carBrandRepositoryContract->deleteCarBrand($id);
        return json_encode($delete);
    }
}
