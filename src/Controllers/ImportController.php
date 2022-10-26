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
use d2gPmPluginCarPartsFinder\Repositories\CarBrandRepository;
use Plenty\Modules\Cloud\Storage\Models\StorageObject;
use Plenty\Modules\Cloud\Storage\Models\StorageObjectList;
use Plenty\Modules\ContentBuilder\Contracts\ContentStorageRepositoryContract;
use Plenty\Modules\Frontend\Services\FileService;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;
use Plenty\Modules\Item\Variation\Contracts\VariationSearchRepositoryContract;
use Plenty\Modules\Item\Variation\Models\Variation;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;
use Plenty\Modules\Webshop\ItemSearch\Factories\VariationSearchFactory;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Log\Loggable;

class ImportController extends Controller
{
    use Loggable;

    public function clean(){

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        $database->query(VariationHSNTSN::class)
            ->where('id', '>', 0)
            ->delete();
        $database->query(HSNTSN::class)
            ->where('id', '>', 0)
            ->delete();
        $database->query(Car::class)
            ->where('id', '>', 0)
            ->delete();
        $database->query(CarPlatform::class)
            ->where('id', '>', 0)
            ->delete();
        $database->query(CarType::class)
            ->where('id', '>', 0)
            ->delete();
        $database->query(CarModel::class)
            ->where('id', '>', 0)
            ->delete();
        $database->query(CarBrand::class)
            ->where('id', '>', 0)
            ->delete();
    }



    /**
     * @param Request $r
     * @return mixed
     */

    public function import(Request $r){

        $csv = [];
        $hsntsnVariations = [];

        $this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.start', [
            'url' => $r->get('url')
        ]);

        if($r->has('url')){

            /** @var CsvClient $client */
            $client = pluginApp(CsvClient::class);
            $result = $client->get($r->get('url'));

            if(!empty($result['result'])){
                $csvArray = explode(PHP_EOL, $result['result']);

                foreach($csvArray as $csvArrayEntry){

                    $csv[] = str_getcsv($csvArrayEntry, ';');
                }
            }
        }

        if(is_array($csv)){

            // Validate Headers

            $headerRow = $csv[0];

            $indexKtype         = 0;
            $indexKey1          = 1;
            $indexKey2          = 2;
            $indexItemNo        = 3;
            $indexSelection1    = 4;
            $indexSelection2    = 5;
            $indexSelection3    = 6;
            $indexSelection4    = 7;

            foreach($headerRow as $index => $headerEntry){

                if(trim($headerEntry) == 'EbayPartsFitmentItemValueKType'){
                    $indexKtype = $index;
                }
                if(trim($headerEntry) == 'Schlüsselnummer 1'){
                    $indexKey1 = $index;
                }
                if(trim($headerEntry) == 'Schlüsselnummer 2'){
                    $indexKey2 = $index;
                }
                if(trim($headerEntry) == 'ItemNo'){
                    $indexItemNo = $index;
                }
                if(trim($headerEntry) == 'Auswahl 1'){
                    $indexSelection1 = $index;
                }
                if(trim($headerEntry) == 'Auswahl 2'){
                    $indexSelection2 = $index;
                }
                if(trim($headerEntry) == 'Auswahl 3'){
                    $indexSelection3 = $index;
                }
                if(trim($headerEntry) == 'Auswahl 4'){
                    $indexSelection4 = $index;
                }
            }

            $this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.import', [
                'indexKtype' => $indexKtype,
                'indexKey1' => $indexKey1,
                'indexKey2' => $indexKey2,
                'indexItemNo' => $indexItemNo,
                'indexSelection1' => $indexSelection1,
                'indexSelection2' => $indexSelection2,
                'indexSelection3' => $indexSelection3,
                'indexSelection4' => $indexSelection4,
            ]);

            if(
                $indexKtype         !== null AND
                $indexKey1          !== null AND
                $indexKey2          !== null AND
                $indexItemNo        !== null AND
                $indexSelection1    !== null AND
                $indexSelection2    !== null AND
                $indexSelection3    !== null AND
                $indexSelection4    !== null
            ){
                $maxRows = count($csv);

                for($row=1;$row<=$maxRows;$row++){

                    $brand      = null;
                    $model      = null;
                    $type       = null;
                    $platform   = null;
                    $car        = null;
                    $hsntsn0    = null;
                    $hsntsn1    = null;
                    $hsntsn2    = null;

                    $this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.importRow', [
                        'row' => $row,
                        'content' => $csv[$row]
                    ]);


                    /*
                     * FirstOrCreate Car
                     */

                    $brandName = trim($csv[$row][$indexSelection1]);

                    if(!empty($brandName) AND $brandName != '#NV'){
                        $this->getLogger("ImportController")->debug('d2gPmPluginCarPartsFinder::ImportController.brandName', $brandName);

                        /** @var CarBrandRepositoryContract $brandRepository */
                        $brandRepository = pluginApp(CarBrandRepositoryContract::class);

                        /** @var CarBrand $brand */
                        $brand = $brandRepository->firstOrCreate(['name' =>$brandName ]);


                        $modelName = trim($csv[$row][$indexSelection2]);

                        if(!empty($modelName) AND $modelName != '#NV'){

                            /** @var CarModelRepositoryContract $modelRepository */
                            $modelRepository = pluginApp(CarModelRepositoryContract::class);

                            /** @var CarModel $model */
                            $model = $modelRepository->firstOrCreate($brand->id, ['name' => $modelName]);


                            $typeName = trim($csv[$row][$indexSelection3]);

                            if(!empty($typeName) AND $typeName != '#NV'){

                                /** @var CarTypeRepositoryContract $typeRepository */
                                $typeRepository = pluginApp(CarTypeRepositoryContract::class);

                                /** @var CarType $model */
                                $type = $typeRepository->firstOrCreate($model->id, ['name' => $typeName]);


                                $platformName = trim($csv[$row][$indexSelection4]);

                                if(!empty($platformName) AND $platformName != '#NV'){

                                    /** @var CarPlatformRepositoryContract $platformRepository */
                                    $platformRepository = pluginApp(CarPlatformRepositoryContract::class);

                                    /** @var CarPlatform $model */
                                    $platform = $platformRepository->firstOrCreate($type->id, ['name' => $platformName]);


                                    $carName = $brandName.' '.$modelName.' '.$typeName.' '.$platformName;

                                    if(!empty($carName) AND $carName != '#NV'){

                                        /** @var CarRepositoryContract $carRepository */
                                        $carRepository = pluginApp(CarRepositoryContract::class);

                                        /** @var CarPlatform $model */
                                        $car = $carRepository->firstOrCreate($platform->id, ['name' => $carName]);
                                    }

                                    /** @var HSNTSNRepositoryContract $hsntsnRepositoryContract */
                                    $hsntsnRepositoryContract = pluginApp(HSNTSNRepositoryContract::class);

                                    $hsntsn0 = $hsntsnRepositoryContract->firstOrCreate($car->id, ['hsn' => 'hsn', 'tsn' => 'tsn']);


                                    if(!empty(trim($csv[$row][$indexKey1])) AND trim($csv[$row][$indexKey1]) != '#NV'){

                                        $key1Hsn = substr($csv[$row][$indexKey1], 0, 4);
                                        $key1Tsn = substr($csv[$row][$indexKey1], -3, 3);
                                        $this->getLogger("ImportController")->debug('d2gPmPluginCarPartsFinder::ImportController.import', ['hsn' => $key1Hsn, 'tsn' => $key1Tsn]);

                                        $hsntsn1 = $hsntsnRepositoryContract->firstOrCreate($car->id, ['hsn' => $key1Hsn, 'tsn' => $key1Tsn]);
                                    }

                                    if(!empty(trim($csv[$row][$indexKey2])) AND trim($csv[$row][$indexKey2]) != '#NV'){

                                        $key2Hsn = substr($csv[$row][$indexKey1], 0, 4);
                                        $key2Tsn = substr($csv[$row][$indexKey1], -3, 3);
                                        $this->getLogger("ImportController")->debug('d2gPmPluginCarPartsFinder::ImportController.import', ['hsn' => $key2Hsn, 'tsn' => $key2Tsn]);

                                        $hsntsn2 = $hsntsnRepositoryContract->firstOrCreate($car->id, ['hsn' => $key2Hsn, 'tsn' => $key2Tsn]);
                                    }
                                }
                            }
                        }
                    }

                    if(!empty($hsntsn0)){

                        /*
                         * Search for variation
                         */

                        $variationNumber = trim($csv[$row][$indexItemNo]);

                        if(!empty($variationNumber)){

                            /** @var VariationSearchRepositoryContract $variationRepositoryContract */
                            $variationRepositoryContract = pluginApp(VariationSearchRepositoryContract::class);

                            $variationRepositoryContract->setFilters([
                                'numberExact' => $variationNumber
                            ]);

                            $variations = $variationRepositoryContract->search();

                            $variationsResult = $variations->getResult();
                            //$this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.variations', $variationsResult);

                            if(count($variationsResult) > 0){

                                /** @var Variation $variation */
                                $variation = $variationsResult[0];
                                //$this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.variation', $variation);

                                /*
                                 * Link car & variation
                                 */

                                /** @var VariationHSNTSNRepositoryContract $variationHSNTSNRepositoryContract */
                                $variationHSNTSNRepositoryContract = pluginApp(VariationHSNTSNRepositoryContract::class);

                                $hsntsnVariations[] = $variationHSNTSNRepositoryContract->create($hsntsn0->id, ['variationId' => $variation['id']]);
                                if(!empty($hsntsn1)){
                                    $hsntsnVariations[] = $variationHSNTSNRepositoryContract->create($hsntsn1->id, ['variationId' => $variation['id']]);
                                }
                                if(!empty($hsntsn2)){
                                    $hsntsnVariations[] = $variationHSNTSNRepositoryContract->create($hsntsn2->id, ['variationId' => $variation['id']]);
                                }

                            } else {
                                $this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.import', 'Variation '.$variationNumber.' not found');
                            }
                        } else {
                            $this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.import', 'Variation '.$variationNumber.' not found');
                        }
                    }
                }
            }
        }

        $this->getLogger("ImportController")->error('d2gPmPluginCarPartsFinder::ImportController.stop', [
            'url' => $r->get('url')
        ]);

        return json_encode($hsntsnVariations);
    }


}
