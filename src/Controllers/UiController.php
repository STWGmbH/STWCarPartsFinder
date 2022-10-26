<?php

namespace d2gPmPluginCarPartsFinder\Controllers;

use d2gPmPluginCarPartsFinder\Clients\CsvClient;
use d2gPmPluginCarPartsFinder\Contracts\CarBrandRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarModelRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarPlatformRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarTypeRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\HSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\VariationHSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\CarBrand;
use d2gPmPluginCarPartsFinder\Models\CarModel;
use d2gPmPluginCarPartsFinder\Models\CarPlatform;
use d2gPmPluginCarPartsFinder\Models\CarType;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use Illuminate\Support\Collection;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;
use Plenty\Modules\Item\Variation\Contracts\VariationSearchRepositoryContract;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Log\Loggable;

class UiController extends Controller
{
    use Loggable;

    public function indexVariations(Request $r){
        $entries = [];

        /** @var VariationSearchRepositoryContract $variationSearchRepositoryContract */
        $variationSearchRepositoryContract = pluginApp(VariationSearchRepositoryContract::class);

        // Pagination filtering
        if($r->has('page')){
            $variationSearchRepositoryContract->setSearchParams(
                [
                    'page' => $r->get('page'),
                    'itemsPerPage' => $r->get('itemsPerPage')
                ]
            );
        } elseif ($r->has('itemsPerPage')){
            $variationSearchRepositoryContract->setSearchParams(
                [
                    'itemsPerPage' => $r->get('itemsPerPage')
                ]
            );
        }

        //  Data filtering
        $variationSearchRepositoryContract->setFilters(
            [
                'itemId' => $r->get('itemId'),
                'numberFuzzy' => $r->get('numberFuzzy'),
                'id' => $r->get('id'),
            ]
        );

        $variationSearchRepositoryContractSearch = $variationSearchRepositoryContract->search();

        $resultVariations = $variationSearchRepositoryContractSearch->paginate()->toArray();

        foreach($resultVariations['entries'] as $resultVariation){

            if (!is_array($resultVariation)) {
                $resultVariation = $resultVariation->toArray();
            }

            /** @var DataBase $database */
            $database = pluginApp(DataBase::class);

            /** @var VariationHSNTSN[] $carList */
            $relations = $database->query(VariationHSNTSN::class)->where('variationId', $resultVariation['id'])->get();

            $name = $resultVariation['name'];

            if(empty($name)){
                /** @var ItemRepositoryContract $itemRepositoryContract */
                $itemRepositoryContract = pluginApp(ItemRepositoryContract::class);
                /** @var Item $item */
                $item = $itemRepositoryContract->show($resultVariation['itemId'], [], 'de');
                $name = $item->texts[0]->name1;
            }

            $entries[] = [
                'itemId' => $resultVariation['itemId'],
                'id' => $resultVariation['id'],
                'variationNumber' => $resultVariation['number'],
                'name' => $name,
                'relations' => count($relations)
            ];
        }

        $resultVariations['entries'] = $entries;

        return json_encode($resultVariations);
    }


    public function showVariation(Request $r, $variationId){

        $cars = [];
        $connections = [];
        $variation = $variationId;

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

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var VariationRepositoryContract $variationRepositoryContract */
        $variationRepositoryContract = pluginApp(VariationRepositoryContract::class);

        $variationDetail = $variationRepositoryContract->findById($variationId);

        /** @var VariationHSNTSN[] $carList */
        $connections = $database->query(VariationHSNTSN::class)
            ->where('variationId', $variationId)
            ->limit($result['itemsPerPage'])
            ->offset($result['itemsPerPage']*($result['page']-1))
            ->get();

        $result['totalsCount'] = $database->query(VariationHSNTSN::class)
            ->where('variationId', $variationId)
            ->count();

        /** @var VariationHSNTSN $connection */
        foreach($connections as $connection){

            /** @var HSNTSN[] $hsntsnList */
            $hsntsnList = $database->query(HSNTSN::class)->where('id', $connection->hsntsnId)->get();

            foreach($hsntsnList as $hsntsn){

                /** @var Car[] $carList */
                $carList = $database->query(Car::class)->where('id', $hsntsn->carId)->get();

                foreach($carList as $car){
                    $cars[] = [
                        'id' => $hsntsn->id,
                        'car' => $car->name,
                        'hsn-tsn' => $hsntsn->hsn.' '.$hsntsn->tsn
                    ];
                }
            }
        }

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];

        $result['variation'] = $variation;
        $result['variationDetail'] = $variationDetail;
        $result['cars'] = $cars;
        $result['connections'] = $connections;

        return json_encode($result);
    }

    public function indexVariationsByCategory(Request $r)
    {
        $categoryId = $r->get('categoryId');
        $hsntsnId = $r->get('hsntsnId');

        $variationIds = [];

        $categoryVariationsIds = [];
        /** @var VariationSearchRepositoryContract $variationSearchRepositoryContract */
        $variationSearchRepositoryContract = pluginApp(VariationSearchRepositoryContract::class);
        $variationSearchRepositoryContract->setFilters([
            'categoryId' => $categoryId
        ]);

        $variationSearchRepositoryContractSearch = $variationSearchRepositoryContract->search();

        $result = $variationSearchRepositoryContractSearch->getResult();

        foreach($result as $resultVariation){

            if (!is_array($resultVariation)) {
                $resultVariation = $resultVariation->toArray();
            }

            $categoryVariationsIds[] = $resultVariation['id'];
        }

        $hsntsnIds = [];

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var HSNTSN[] $hsntsnList */
        $hsntsnList = $database->query(HSNTSN::class)
            ->where('id', '=', $hsntsnId)
            ->get();

        if(count($hsntsnList)){
            if($hsntsnList[0]->hsn == 'hsn'){

                /** @var HSNTSN[] $hsntsnListByCar */
                $hsntsnListByCar = $database->query(HSNTSN::class)
                    ->where('carId', '=', $hsntsnList[0]->carId)
                    ->get();

                foreach($hsntsnListByCar as $item){
                    $hsntsnIds[] = $item->id;
                }
            }

            $hsntsnIds[] = $hsntsnList[0]->id;
        }

        /** @var VariationHSNTSN[] $variationHSNTSNList */
        $variationHSNTSNList = $database->query(VariationHSNTSN::class)
            ->whereIn('variationId', $categoryVariationsIds)
            ->get();

        // Check if is in database result

        $variationHSNTSNCollection = Collection::make($variationHSNTSNList);

        foreach($categoryVariationsIds as $categoryVariationsId){
            $variationHSNTSNCollectionWhere = $variationHSNTSNCollection->where('variationId', $categoryVariationsId);

            if($variationHSNTSNCollectionWhere->count() > 0){
                $variationHSNTSNCollectionWhereIn = $variationHSNTSNCollectionWhere->whereIn('hsntsnId', $hsntsnIds);

                if($variationHSNTSNCollectionWhereIn->count() > 0){
                    $variationIds[] = [
                        'categoryVariationsId' => $categoryVariationsId,
                        'variationHSNTSNCollectionWhere' => $variationHSNTSNCollectionWhere,
                        'variationHSNTSNCollectionWhereIn' => $variationHSNTSNCollectionWhereIn
                    ];
                }
            } else{
                $variationIds[] = [
                    'categoryVariationsId' => $categoryVariationsId,
                    'variationHSNTSNCollectionWhere' => null,
                    'variationHSNTSNCollectionWhereIn' => null
                ];
            }
        }


        return json_encode([
            'hsntsnIds' => $hsntsnIds,
            'variationIds' => $variationIds,
        ]);
    }





    /**
     * @param Request $r
     * @return mixed
     */

    public function import(Request $r){

        if($r->has('batch')){

            $batch = $r->get('batch');

            /** @var CarBrandRepositoryContract $brandRepository */
            $brandRepository = pluginApp(CarBrandRepositoryContract::class);

            /** @var CarModelRepositoryContract $modelRepository */
            $modelRepository = pluginApp(CarModelRepositoryContract::class);

            /** @var CarTypeRepositoryContract $typeRepository */
            $typeRepository = pluginApp(CarTypeRepositoryContract::class);

            /** @var CarPlatformRepositoryContract $platformRepository */
            $platformRepository = pluginApp(CarPlatformRepositoryContract::class);

            /** @var CarRepositoryContract $carRepository */
            $carRepository = pluginApp(CarRepositoryContract::class);

            /** @var HSNTSNRepositoryContract $hsntsnRepositoryContract */
            $hsntsnRepositoryContract = pluginApp(HSNTSNRepositoryContract::class);


            foreach($batch as $brandName => $models){
                //$this->getLogger("BatchController")->error('d2gPmPluginCarPartsFinder::BatchController.brandName', $brandName);

                /** @var CarBrand $brand */
                $brand = $brandRepository->firstOrCreate(['name' => (string)$brandName]);

                foreach($models as $modelName => $types){
                    //$this->getLogger("BatchController")->error('d2gPmPluginCarPartsFinder::BatchController.modelName', $modelName);

                    /** @var CarModel $model */
                    $model = $modelRepository->firstOrCreate($brand->id, ['name' => (string)$modelName]);

                    foreach($types as $typeName => $platforms){
                        //$this->getLogger("BatchController")->error('d2gPmPluginCarPartsFinder::BatchController.typeName', $typeName);

                        /** @var CarType $type */
                        $type = $typeRepository->firstOrCreate($model->id, ['name' => (string)$typeName]);

                        foreach($platforms as $platformName => $hsnTsns){
                            //$this->getLogger("BatchController")->error('d2gPmPluginCarPartsFinder::BatchController.platformName', $platformName);

                            /** @var CarPlatform $platform */
                            $platform = $platformRepository->firstOrCreate($type->id, ['name' => (string)$platformName]);


                            $carName = $brandName.' '.$modelName.' '.$typeName.' '.$platformName;

                            /** @var CarPlatform $model */
                            $car = $carRepository->firstOrCreate($platform->id, ['name' => (string)$carName]);

                            $hsntsn0 = $hsntsnRepositoryContract->firstOrCreate($car->id, ['hsn' => 'hsn', 'tsn' => 'tsn']);

                            foreach($hsnTsns as $hsnTsn => $value){

                                $hsn = substr($hsnTsn, 0, 4);
                                $tsn = substr($hsnTsn, -3, 3);
                                $hsntsn = $hsntsnRepositoryContract->firstOrCreate($car->id, ['hsn' => $hsn, 'tsn' => $tsn]);
                            }
                        }
                    }
                }
            }
        }

        return json_encode($r->all());
    }

    public function importItemCars(Request $r){
        $imported = [];

        if($r->has('batch')){
            $batch = $r->get('batch');

            /** @var VariationHSNTSNRepositoryContract $variationHSNTSNRepositoryContract */
            $variationHSNTSNRepositoryContract = pluginApp(VariationHSNTSNRepositoryContract::class);

            foreach($batch as $variationId => $hsntsnList){
                $imported[] = $variationHSNTSNRepositoryContract->createBatch($variationId, $hsntsnList);
            }
        }

        return json_encode($imported);
    }

    public function indexCars(Request $r){

        $vehicleId = intval($r->get('vehicleId'));
        $vehicleName = $r->get('vehicleName');

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

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

        if(!empty($vehicleId)) {
            $hsntsnList = $database->query(HSNTSN::class)
                ->where('id','=', $vehicleId)
                ->limit($result['itemsPerPage'])
                ->offset($result['itemsPerPage']*($result['page']-1))
                ->get();

            $result['totalsCount'] = $database->query(HSNTSN::class)
                ->where('id','=', $vehicleId)
                ->count();
        } else{
            $hsntsnList = $database->query(HSNTSN::class)
                ->limit($result['itemsPerPage'])
                ->offset($result['itemsPerPage']*($result['page']-1))
                ->get();

            $result['totalsCount'] = $database->query(HSNTSN::class)
                ->count();
        }

        $items = [];
        /** @var HSNTSN $hsntsn */
        foreach($hsntsnList as $hsntsn){
            if(!empty($vehicleName)) {
                $carList = $database->query(Car::class)
                    ->where('id', $hsntsn->carId)
                    ->where('name', '=', $vehicleName)
                    ->get();
            } else {
                $carList = $database->query(Car::class)
                    ->where('id', $hsntsn->carId)
                    ->get();
            }

            if(!empty($carList[0])){
                $items[] = [
                    'id' => $hsntsn->id,
                    'car' => $carList[0]->name,
                    'hsn-tsn' => $hsntsn->hsn . ' ' . $hsntsn->tsn
                ];
            }
        }
        $result['entries'] = $items;

        $result['lastPageNumber'] = ceil($result['totalsCount'] / $result['itemsPerPage']);
        $result['isLastPage'] =  $result['page'] == $result['lastPageNumber'];

        return json_encode($result);
    }

    public function indexItems(){

        $items = [];

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var VariationHSNTSN[] $items */
        $items = $database->query(VariationHSNTSN::class)->get();

        return json_encode($items);
    }
}
