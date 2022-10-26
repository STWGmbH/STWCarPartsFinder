<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Contracts\CarBrandRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarModelRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarPlatformRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarTypeRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\HSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use Illuminate\Support\Collection;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Log\Loggable;

class CeresController extends Controller
{
    use Loggable;

    /**
     * @param Request $r
     * @return string
     */
    public function search(Request $r): string
    {
        $platformId = $r->get('platformId', null);
        $hsn = $r->get('hsn', null);
        $tsn = $r->get('tsn', null);

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        $result = [];

        if(!empty($hsn) && !empty($tsn)){


            /** @var HSNTSN[] $hsntsnList */
            $hsntsnList = $database->query(HSNTSN::class)
                ->where('hsn', '=', $hsn)
                ->where('tsn', '=', $tsn)
                ->get();

            if(count($hsntsnList) > 0){

                /** @var HSNTSN $hsntsnEntry */
                foreach($hsntsnList as $hsntsnEntry){

                    /** @var Car[] $carList */
                    $carList = $database->query(HSNTSN::class)
                        ->where('id', '=', $hsntsnEntry->carId)
                        ->get();

                    if(count($carList) > 0){

                        $result[] = [
                            'car' => $carList[0],
                            'list' => $hsntsnList
                        ];
                    }
                }
            }

        } elseif(!empty($platformId)){

            /** @var Car[] $carList */
            $carList = $database->query(Car::class)
                ->where('platformId', '=', $platformId)
                ->get();

            if(count($carList) > 0){

                /** @var Car $carEntry */
                foreach($carList as $carEntry){

                    /** @var HSNTSN[] $hsntsnList */
                    $hsntsnList = $database->query(HSNTSN::class)
                        ->where('carId', '=', $carEntry->id)
                        ->get();

                    if(count($hsntsnList) > 0){

                        $result[] = [
                            'car' => $carEntry,
                            'list' => $hsntsnList
                        ];

                    }
                }
            }
        }

        return json_encode($result);
    }

    /**
     * @return string
     */
    public function indexBrands(): string
    {
        /** @var CarBrandRepositoryContract $carBrandRepositoryContract */
        $carBrandRepositoryContract = pluginApp(CarBrandRepositoryContract::class);
        $index = $carBrandRepositoryContract->getCarBrandList();

        return json_encode($index);
    }

    /**
     * @param int $brandId
     * @return string
     */
    public function indexModels(int $brandId): string
    {
        /** @var CarModelRepositoryContract $carModelRepositoryContract */
        $carModelRepositoryContract = pluginApp(CarModelRepositoryContract::class);
        $index = $carModelRepositoryContract->getCarModelList($brandId);

        return json_encode($index);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @return string
     */
    public function indexTypes(int $brandId, int $modelId): string
    {
        /** @var CarTypeRepositoryContract $carTypeRepositoryContract */
        $carTypeRepositoryContract = pluginApp(CarTypeRepositoryContract::class);
        $index = $carTypeRepositoryContract->getCarTypeList($modelId);

        return json_encode($index);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @return string
     */
    public function indexPlatforms(int $brandId, int $modelId, int $typeId): string
    {
        /** @var CarPlatformRepositoryContract $carPlatformRepositoryContract */
        $carPlatformRepositoryContract = pluginApp(CarPlatformRepositoryContract::class);
        $index = $carPlatformRepositoryContract->getCarPlatformList($typeId);

        return json_encode($index);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @return string
     */
    public function indexCars(int $brandId, int $modelId, int $typeId, int $platformId): string
    {
        /** @var CarRepositoryContract $carRepositoryContract */
        $carRepositoryContract = pluginApp(CarRepositoryContract::class);
        $index = $carRepositoryContract->getCarList($platformId);

        return json_encode($index);
    }

    /**
     * @param int $brandId
     * @param int $modelId
     * @param int $typeId
     * @param int $platformId
     * @param int $carId
     * @return string
     */
    public function indexHSNTSN(int $brandId, int $modelId, int $typeId, int $platformId, int $carId): string
    {
        /** @var HSNTSNRepositoryContract $hsntsnRepositoryContract */
        $hsntsnRepositoryContract = pluginApp(HSNTSNRepositoryContract::class);
        $index = $hsntsnRepositoryContract->index($carId);

        return json_encode($index);
    }

    /**
     * @param  Request $request
     * @return string
     */
    public function store(Request $request): string
    {
        /** @var FrontendSessionStorageFactoryContract $sessionStorage */
        $sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);
        $current = [
            'car' => null,
            'hsntsn' => null,
        ];

        $this->getLogger("CeresController")->error('d2gPmPluginCarPartsFinder::CeresController.store', $request->all());

        if($request->has('hsntsnId')){

            /** @var DataBase $database */
            $database = pluginApp(DataBase::class);

            /** @var HSNTSN[] $hsntsnList */
            $hsntsnList = $database->query(HSNTSN::class)
                ->where('id', '=', $request->get('hsntsnId'))
                ->get();

            $this->getLogger("CeresController")->error('d2gPmPluginCarPartsFinder::CeresController.store', $hsntsnList);

            if(isset($hsntsnList[0])){

                /** @var Car[] $carList */
                $carList = $database->query(Car::class)
                    ->where('id', '=', $hsntsnList[0]->carId)
                    ->get();

                $this->getLogger("CeresController")->error('d2gPmPluginCarPartsFinder::CeresController.store', $carList);

                if(isset($carList[0])){
                    $sessionStorage->getPlugin()->setValue('CAR_PARTS_FILTER_CAR', $carList[0]);
                    $current['car'] = $carList[0];

                    $sessionStorage->getPlugin()->setValue('CAR_PARTS_FILTER_HSNTSN', $hsntsnList[0]);
                    $current['hsntsn'] = $hsntsnList[0];

                    return json_encode(['current' => $current]);
                }
            }
        }

        $current = [
            'car' => null,
            'hsntsn' => null,
        ];

        $sessionStorage->getPlugin()->unsetKey('CAR_PARTS_FILTER_CAR');
        $sessionStorage->getPlugin()->unsetKey('CAR_PARTS_FILTER_HSNTSN');

        return json_encode(['current' => $current]);
    }


    /**
     * @return string
     */
    public function get(): string
    {
        $current = [
            'car' => null,
            'hsntsn' => null,
        ];

        /** @var FrontendSessionStorageFactoryContract $sessionStorage */
        $sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);
        $current['car'] = $sessionStorage->getPlugin()->getValue('CAR_PARTS_FILTER_CAR');
        $current['hsntsn'] = $sessionStorage->getPlugin()->getValue('CAR_PARTS_FILTER_HSNTSN');

        return json_encode(['current' => $current]);
    }

    /**
     * @return string
     */
    public function delete(): string
    {
        $current = [
            'car' => null,
            'hsntsn' => null,
        ];

        /** @var FrontendSessionStorageFactoryContract $sessionStorage */
        $sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);
        $sessionStorage->getPlugin()->unsetKey('CAR_PARTS_FILTER_CAR');
        $sessionStorage->getPlugin()->unsetKey('CAR_PARTS_FILTER_HSNTSN');

        return json_encode(['current' => $current]);
    }

    /**
     * @param int $variationId
     * @return bool
     */
    public function isLinked(int $variationId): bool
    {

        return true;
    }
}
