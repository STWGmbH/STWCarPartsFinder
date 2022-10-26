<?php

namespace d2gPmPluginCarPartsFinder\Repositories;

use Carbon\Carbon;
use d2gPmPluginCarPartsFinder\Contracts\CarModelRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\CarModel;
use d2gPmPluginCarPartsFinder\Models\CarPlatform;
use d2gPmPluginCarPartsFinder\Models\CarType;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use d2gPmPluginCarPartsFinder\Validators\CarModelValidator;
use Illuminate\Support\Collection;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class CarModelRepository implements CarModelRepositoryContract
{

    /**
     * Add a new brand
     *
     * @param int $brandId
     * @param array $data
     * @return CarModel
     * @throws ValidationException
     */

    public function firstOrCreate($brandId, array $data):CarModel
    {
        try {
            CarModelValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarModel[] $carModelList */
        $carModelList = $database->query(CarModel::class)
            ->where('brandId', '=', $brandId)
            ->where('name', '=', $data['name'])
            ->get();

        if(!empty($carModelList)){
            return $carModelList[0];
        }

        /** @var CarModel $carModel */
        $carModel = pluginApp(CarModel::class);
        $carModel->brandId = $brandId;
        $carModel->name = $data['name'];
        $carModel->createdAt = Carbon::now()->toDateTimeString();
        $carModel->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carModel);

        return $carModel;
    }

    /**
     * Add a new brand
     *
     * @param int $brandId
     * @param array $data
     * @return CarModel
     * @throws ValidationException
     */

    public function createCarModel($brandId, array $data):CarModel
    {
        try {
            CarModelValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarModel $carModel */
        $carModel = pluginApp(CarModel::class);
        $carModel->brandId = $brandId;
        $carModel->name = $data['name'];
        $carModel->position = $data['position'];
        $carModel->createdAt = Carbon::now()->toDateTimeString();
        $carModel->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carModel);

        return $carModel;
    }

    /**
     * List all items of the To Do list
     *
     * @@param int $brandId
     * @return CarModel[]
     */
    public function getCarModelList($brandId): array
    {
        $result = Collection::make([]);

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarModel[] $carModelList */
        $carModelList = $database->query(CarModel::class)
            ->where('brandId', '=', $brandId)
            ->orderBy('position')
            ->get();

        $collection = Collection::make($carModelList)->groupBy('position');

        foreach($collection as $position => $entries){

            /** @var CarModel[] $carModelList */
            $carModelListByPositions = $database->query(CarModel::class)
                ->where('brandId', '=', $brandId)
                ->where('position', $position)
                ->orderBy('name')
                ->get();

            foreach($carModelListByPositions as $carModelListByPosition) {
                $result->push($carModelListByPosition);
            }
        }

        return $result->toArray();
    }

    /**
     * Get the item
     *
     * @param int $id
     * @return CarModel
     */
    public function getCarModel($id): CarModel
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarModel[] $carModelList */
        $carModelList = $database->query(CarModel::class)
            ->where('id', '=', $id)
            ->get();

        $carModel = $carModelList[0];
        return $carModel;
    }

    /**
     * Update the status of the item
     *
     * @param int $id
     * @param array $data
     * @return CarModel
     * @throws ValidationException
     */
    public function updateCarModel($id, array $data): CarModel
    {
        try {
            CarModelValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarModel[] $carModelList */
        $carModelList = $database->query(CarModel::class)
            ->where('id', '=', $id)
            ->get();

        $carModel = $carModelList[0];
        $carModel->name = $data['name'];
        $carModel->position = $data['position'];
        $carModel->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carModel);

        return $carModel;
    }

    public function deleteCarModel($id): CarModel
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarModel[] $carModelList */
        $carModelList = $database->query(CarModel::class)
            ->where('id', '=', $id)
            ->get();

        /** @var CarModel $carModel */
        $carModel = $carModelList[0];


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

        return $carModel;
    }
}
