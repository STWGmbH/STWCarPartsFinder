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
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Log\Loggable;

class BatchController extends Controller
{
    use Loggable;


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

    public function indexCars(){

        $cars = [];

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var Car[] $carList */
        $carList = $database->query(Car::class)->get();

        /** @var Car $car */
        foreach($carList as $car){

            $cars[$car->name] = [];

            /** @var Car[] $hsntsnList */
            $hsntsnList = $database->query(HSNTSN::class)
                ->where('carId', '=', $car->id)
                ->get();

            /** @var HSNTSN $hsntsn */
            foreach($hsntsnList as $hsntsn){
                $cars[$car->name][$hsntsn->hsn.' '.$hsntsn->tsn] = $hsntsn->id;
            }
        }

        return json_encode($cars);
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
