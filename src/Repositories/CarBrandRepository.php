<?php

namespace d2gPmPluginCarPartsFinder\Repositories;

use Carbon\Carbon;
use d2gPmPluginCarPartsFinder\Contracts\CarBrandRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\CarBrand;
use d2gPmPluginCarPartsFinder\Models\CarModel;
use d2gPmPluginCarPartsFinder\Models\CarPlatform;
use d2gPmPluginCarPartsFinder\Models\CarType;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use d2gPmPluginCarPartsFinder\Validators\CarBrandValidator;
use Illuminate\Support\Collection;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Log\Loggable;

class CarBrandRepository implements CarBrandRepositoryContract
{
    use Loggable;

    /**
     * Search for brand by name or create a new entry
     *
     * @param array $data
     * @return CarBrand
     * @throws ValidationException
     */

    public function firstOrCreate(array $data):CarBrand
    {
        try {
            CarBrandValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarBrand[] $carModelList */
        $carBrandList = $database->query(CarBrand::class)
            ->where('name', '=', $data['name'])
            ->get();


        if(count($carBrandList)){
            return $carBrandList[0];
        }

        /** @var CarBrand $carBrand */
        $carBrand = pluginApp(CarBrand::class);
        $carBrand->name = $data['name'];
        $carBrand->createdAt = Carbon::now()->toDateTimeString();
        $carBrand->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carBrand);

        return $carBrand;
    }


    /**
     * Add a new brand
     *
     * @param array $data
     * @return CarBrand
     * @throws ValidationException
     */

    public function createCarBrand(array $data):CarBrand
    {
        try {
            CarBrandValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarBrand $carBrand */
        $carBrand = pluginApp(CarBrand::class);
        $carBrand->name = $data['name'];
        $carBrand->position = $data['position'];
        $carBrand->createdAt = Carbon::now()->toDateTimeString();
        $carBrand->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carBrand);

        return $carBrand;
    }

    /**
     * List all items of the To Do list
     *
     * @return CarBrand[]
     */
    public function getCarBrandList(): array
    {
        $result = Collection::make([]);

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarBrand[] $carBrandList */
        $carBrandList = $database->query(CarBrand::class)
            ->orderBy('position')
            ->get();

        $collection = Collection::make($carBrandList)->groupBy('position');

        foreach($collection as $position => $entries){

            /** @var CarBrand[] $carBrandList */
            $carBrandListByPositions = $database->query(CarBrand::class)
                ->orderBy('name')
                ->where('position', $position)
                ->get();

            foreach($carBrandListByPositions as $carBrandListByPosition) {
                $result->push($carBrandListByPosition);
            }
        }

        return $result->toArray();
    }

    /**
     * Get the item
     *
     * @param int $id
     * @return CarBrand
     */
    public function getCarBrand($id): CarBrand
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarBrand[] $carModelList */
        $carBrandList = $database->query(CarBrand::class)
            ->where('id', '=', $id)
            ->get();

        $carBrand = $carBrandList[0];
        return $carBrand;
    }

    /**
     * Update the status of the item
     *
     * @param int $id
     * @param array $data
     * @return CarBrand
     * @throws ValidationException
     */
    public function updateCarBrand($id, array $data): CarBrand
    {
        try {
            CarBrandValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarBrand[] $carBrandList */
        $carBrandList = $database->query(CarBrand::class)
            ->where('id', '=', $id)
            ->get();

        $carBrand = $carBrandList[0];
        $carBrand->name = $data['name'];
        $carBrand->position = $data['position'];
        $carBrand->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carBrand);

        return $carBrand;
    }

    public function deleteCarBrand($id): CarBrand
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarBrand[] $carBrandList */
        $carBrandList = $database->query(CarBrand::class)
            ->where('id', '=', $id)
            ->get();

        /** @var CarBrand $carBrand */
        $carBrand = $carBrandList[0];


        /** @var CarModel[] $carModelList */
        $carModelList = $database->query(CarModel::class)
            ->where('brandId', '=', $carBrand->id)
            ->get();

        foreach ($carModelList as $carModel) {

            /** @var CarType[] $carTypeList */
            $carTypeList = $database->query(CarType::class)
                ->where('modelId', '=', $carModel->id)
                ->get();

            foreach($carTypeList as $carType){

                /** @var CarPlatform[] $carPlatformList */
                $carPlatformList = $database->query(CarPlatform::class)
                    ->where('typeId', '=', $carType->id)
                    ->get();

                foreach($carPlatformList as $carPlatform){

                    /** @var Car[] $carList */
                    $carList = $database->query(Car::class)
                        ->where('platformId', '=', $carPlatform->id)
                        ->get();

                    /** @var Car $car */
                    foreach($carList as $car){

                        /** @var HSNTSN[] $list */
                        $hsnTsnlist = $database->query(HSNTSN::class)
                            ->where('carId', '=', $car->id)
                            ->get();

                        /** @var HSNTSN $hsnTsn */
                        foreach($hsnTsnlist as $hsnTsn){
                            /** @var VariationHSNTSN[] $list */
                            $variationHsnTsnlist = $database->query(VariationHSNTSN::class)
                                ->where('hsntsnId', '=', $hsnTsn->id)
                                ->get();

                            foreach($variationHsnTsnlist as $variationHsnTsn){
                                $database->delete($variationHsnTsn);
                            }

                            $database->delete($hsnTsn);
                        }

                        $database->delete($car);
                    }

                    $database->delete($carPlatform);
                }

                $database->delete($carType);
            }

            $database->delete($carModel);
        }

        $database->delete($carBrand);

        return $carBrand;
    }
}
