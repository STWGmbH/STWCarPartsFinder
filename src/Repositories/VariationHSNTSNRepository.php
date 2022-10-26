<?php

namespace d2gPmPluginCarPartsFinder\Repositories;

use Carbon\Carbon;
use Ceres\Helper\SearchOptions;
use d2gPmPluginCarPartsFinder\Contracts\VariationHSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use d2gPmPluginCarPartsFinder\Validators\VariationHSNTSNValidator;
use Illuminate\Support\Collection;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Category\Contracts\CategoryRepositoryContract;
use Plenty\Modules\Category\Models\Category;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Manufacturer\Contracts\ManufacturerRepositoryContract;
use Plenty\Modules\Item\Manufacturer\Models\Manufacturer;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;
use Plenty\Modules\Item\Variation\Contracts\VariationSearchRepositoryContract;
use Plenty\Modules\Item\Variation\Models\Variation;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Modules\Webshop\ItemSearch\Factories\VariationSearchFactory;
use Plenty\Modules\Webshop\ItemSearch\SearchPresets\CategoryItems;
use Plenty\Modules\Webshop\ItemSearch\Services\ItemSearchService;
use Plenty\Plugin\Log\Loggable;

class VariationHSNTSNRepository implements VariationHSNTSNRepositoryContract
{

    use Loggable;

    /**
     * create batch
     *
     * @param int $variationId
     * @param array $hsntsnList
     */

    public function createBatch(int $variationId, array $hsntsnList){

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        foreach($hsntsnList as $hsntsn){

            /** @var VariationHSNTSN[] $variationCarList */
            $variationCarList = $database->query(VariationHSNTSN::class)
                ->where('hsntsnId', '=', $hsntsn)
                ->where('variationId', '=', $variationId)
                ->get();


            if(count($variationCarList) <= 0){

                $variationCar = pluginApp(VariationHSNTSN::class);
                $variationCar->hsntsnId = $hsntsn;
                $variationCar->variationId = $variationId;
                $variationCar->createdAt = Carbon::now()->toDateTimeString();
                $variationCar->updatedAt = Carbon::now()->toDateTimeString();
                $database->save($variationCar);
            }
        }
    }

    /**
     * Add a new brand
     *
     * @param int $hsntsnId
     * @param array $data
     * @return array
     * @throws ValidationException
     */

    public function create(int $hsntsnId, array $data):array
    {

        try {
            VariationHSNTSNValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        // Check for duplicates

        /** @var VariationHSNTSN[] $variationCarList */
        $variationCarList = $database->query(VariationHSNTSN::class)
            ->where('hsntsnId', '=', $hsntsnId)
            ->where('variationId', '=', $data['variationId'])
            ->get();

        if(isset($variationCarList[0])){

            /** @var VariationHSNTSN $variationCar */
            $variationCar = $variationCarList[0];

        } else {

            /** @var VariationHSNTSN $variationCar */
            $variationCar = pluginApp(VariationHSNTSN::class);
            $variationCar->hsntsnId = $hsntsnId;
            $variationCar->variationId = $data['variationId'];
            $variationCar->createdAt = Carbon::now()->toDateTimeString();
            $variationCar->updatedAt = Carbon::now()->toDateTimeString();
            $database->save($variationCar);
        }

        /** @var VariationRepositoryContract $variationRepositoryContract */
        $variationRepositoryContract = pluginApp(VariationRepositoryContract::class);

        /** @var Variation $variation */
        $variation = $variationRepositoryContract->findById($data['variationId']);

        $item = [];
        $manufacturer = [];

        if(isset($variation->itemId) && $variation->itemId > 0){
            /** @var ItemRepositoryContract $itemRepositoryContract */
            $itemRepositoryContract = pluginApp(ItemRepositoryContract::class);

            /** @var Item $item */
            $item = $itemRepositoryContract->show($variation->itemId);

            if(isset($item->manufacturerId) && $item->manufacturerId > 0){
                /** @var ManufacturerRepositoryContract $manufacturerRepositoryContract */
                $manufacturerRepositoryContract = pluginApp(ManufacturerRepositoryContract::class);

                /** @var Manufacturer $manufacturer */
                $manufacturer = $manufacturerRepositoryContract->findById($item->manufacturerId);
            }
        }

        $this->getLogger("createVariationHSNTSN")->debug('d2gPmPluginCarPartsFinder::VariationHSNTSNRepository.createVariationHSNTSN', [
            'variation' => $variation,
            'item' => $item,
            'manufacturer' => $manufacturer,
        ]);

        $result = [
            'id' => $variationCar->id,
            'hsntsnId' => $variationCar->hsntsnId,
            'variationId' => $variationCar->variationId,
            'number' => $variation->number,
            'name' => $item->texts[0]->name,
            'manufacturer' => $manufacturer->name,
            'createdAt' => $variationCar->createdAt,
            'updatedAt' => $variationCar->updatedAt,
        ];

        return $result;
    }

    /**
     * List all items of the To Do list
     *
     * @@param int $hsntsnId
     * @return VariationHSNTSN[]
     */
    public function index(int $hsntsnId): array
    {
        $result= [];

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var VariationHSNTSN[] $variationCarList */
        $variationCarList = $database->query(VariationHSNTSN::class)->where('hsntsnId', '=', $hsntsnId)->get();

        /** @var VariationHSNTSN $variationCar */
        foreach($variationCarList as $variationCar){
            $this->getLogger("variationCar")->error('d2gPmPluginCarPartsFinder::VariationHSNTSNRepository.variationCar', [
                'variationCar' => $variationCar
            ]);

            /** @var VariationRepositoryContract $variationRepositoryContract */
            $variationRepositoryContract = pluginApp(VariationRepositoryContract::class);

            /** @var Variation $variation */
            $variation = $variationRepositoryContract->findById($variationCar->variationId);

            $itemName = null;

            /** @var ItemRepositoryContract $itemRepositoryContract */
            $itemRepositoryContract = pluginApp(ItemRepositoryContract::class);

            /** @var Item $item */
            $item = $itemRepositoryContract->show($variation->itemId);

            if(isset($item->texts[0]->name)){
                $itemName = $item->texts[0]->name;
            }

            $manufacturerName = null;

            if(!empty($item->manufacturerId)){
                /** @var ManufacturerRepositoryContract $manufacturerRepositoryContract */
                $manufacturerRepositoryContract = pluginApp(ManufacturerRepositoryContract::class);

                /** @var Manufacturer $manufacturer */
                $manufacturer = $manufacturerRepositoryContract->findById($item->manufacturerId);

                $manufacturerName = $manufacturer->name;

            }

            if(!empty($item) AND !empty($variation)){
                $result[] = [
                    'id' => $variationCar->id,
                    'hsntsnId' => $variationCar->hsntsnId,
                    'variationId' => $variationCar->variationId,
                    'number' => $variation->number,
                    'name' => $itemName,
                    'manufacturer' => $manufacturerName,
                    'createdAt' => $variationCar->createdAt,
                    'updatedAt' => $variationCar->updatedAt,
                ];
            }
        }

        return $result;
    }

    /**
     * List all items of the To Do list
     *
     * @@param int $hsntsnId
     * @@param int $categoryId
     * @return array
     */
    public function indexByCategory(int $hsntsnId, int $categoryId): array
    {

        $variationsWithoutConnectionCollection = Collection::make();

        $options = [
            'page'          => 1,
            'itemsPerPage'  => 10000,
            'categoryId'    => $categoryId,
        ];

        //do{

            /** @var VariationSearchFactory $itemList */
            $itemList = CategoryItems::getSearchFactory($options);
            $itemList->hasTag(4);

            /** @var ItemSearchService $itemSearchService */
            $itemSearchService = pluginApp(ItemSearchService::class);
            $searchResults = $itemSearchService->getResults(['itemList' => $itemList]);
            $result = Collection::make($searchResults['itemList']['documents']);

            $variationsWithoutConnectionCollection = $variationsWithoutConnectionCollection->merge($result->keyBy('id')->keys());

            $pageMax = ceil($searchResults['itemList']['total'] / $options['itemsPerPage']);
            $options['page']++;
        //}while($options['page'] <= $pageMax);


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
            ->whereIn('hsntsnId', $hsntsnIds)
            ->get();

        $variationHSNTSNCollection = Collection::make($variationHSNTSNList);

        $variationIds = $variationHSNTSNCollection->keyBy('variationId')->keys();
        $variationIds->merge($variationsWithoutConnectionCollection);

        $this->getLogger("getVariationIdsByCar")->error('d2gPmPluginCarPartsFinder::VariationHSNTSNRepository.getVariationIdsByCar', [
            'variationIds' => $variationIds
        ]);

        return $variationIds->toArray();
    }

    public function delete(array $data):bool
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var VariationHSNTSN[] $variationCarList */
        $variationCarList = $database->query(VariationHSNTSN::class)
            ->where('variationId', $data['variationId'])
            ->where('hsntsnId', $data['hsntsnId'])
            ->get();

        if(isset($variationCarList[0])){
            $database->delete($variationCarList[0]);
        }

        return true;
    }
}
