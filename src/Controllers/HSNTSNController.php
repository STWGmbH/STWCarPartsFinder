<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\HSNTSNRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

class HSNTSNController extends Controller
{

    /**
     * @var HSNTSNRepositoryContract
     */
    private $hsntsnRepositoryContract;

    /**
     * @param HSNTSNRepositoryContract $hsntsnRepositoryContract
     */
    public function __construct(HSNTSNRepositoryContract $hsntsnRepositoryContract)
    {
        $this->hsntsnRepositoryContract = $hsntsnRepositoryContract;
        parent::__construct();
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @param Request $r
     * @return string
     */
    public function index(int $brandId, int $modelId, int $typeId, int $platformId, int $carId, Request $r): string
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

        $hsntsns = $this->hsntsnRepositoryContract->index($carId);

        $result['totalsCount'] = count($hsntsns);

        $hsntsns = array_slice($hsntsns, $result['itemsPerPage']*($result['page']-1), $result['itemsPerPage']);

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];
        $result['entries'] = $hsntsns;

        return json_encode($result);
    }

    /**
     * @param Request $request
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @return string
     */
    public function store(Request $request, int $brandId, int $modelId, int $typeId, int $platformId, int $carId): string
    {
        $store = $this->hsntsnRepositoryContract->create($carId, $request->all());

        return json_encode($store);
    }

    /**
     * @param int $id
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @return string
     */
    public function get(int $brandId, int $modelId, int $typeId, int $platformId, int $carId, int $id): string
    {
        $get = $this->hsntsnRepositoryContract->get($id);

        return json_encode($get);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @return string
     */
    public function update(Request $request, int $brandId, int $modelId, int $typeId, int $platformId, int $carId, int $id): string
    {
        $update = $this->hsntsnRepositoryContract->update($id, $request->all());
        return json_encode($update);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @param int $id
     * @return string
     */
    public function delete(int $brandId, int $modelId, int $typeId, int $platformId, int $carId, int $id): string
    {
        $delete = $this->hsntsnRepositoryContract->delete($id);
        return json_encode($delete);
    }
}
