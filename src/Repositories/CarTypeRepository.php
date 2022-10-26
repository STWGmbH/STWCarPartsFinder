<?php

namespace d2gPmPluginCarPartsFinder\Repositories;

use Carbon\Carbon;
use d2gPmPluginCarPartsFinder\Contracts\CarTypeRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\CarPlatform;
use d2gPmPluginCarPartsFinder\Models\CarType;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use d2gPmPluginCarPartsFinder\Validators\CarTypeValidator;
use Illuminate\Support\Collection;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class CarTypeRepository implements CarTypeRepositoryContract
{
    /**
     * Add a new brand
     *
     * @param int $modelId
     * @param array $data
     * @return CarType
     * @throws ValidationException
     */

    public function firstOrCreate($modelId, array $data):CarType
    {
        try {
            CarTypeValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarType[] $carTypeList */
        $carTypeList = $database->query(CarType::class)
            ->where('modelId', '=', $modelId)
            ->where('name', '=', $data['name'])
            ->get();

        if(!empty($carTypeList)){
            return $carTypeList[0];
        }

        /** @var CarType $carType */
        $carType = pluginApp(CarType::class);
        $carType->modelId = $modelId;
        $carType->name = $data['name'];
        $carType->createdAt = Carbon::now()->toDateTimeString();
        $carType->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carType);

        return $carType;
    }

    /**
     * Add a new brand
     *
     * @param int $modelId
     * @param array $data
     * @return CarType
     * @throws ValidationException
     */

    public function createCarType($modelId, array $data):CarType
    {
        try {
            CarTypeValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarType $carType */
        $carType = pluginApp(CarType::class);
        $carType->modelId = $modelId;
        $carType->name = $data['name'];
        $carType->position = $data['position'];
        $carType->createdAt = Carbon::now()->toDateTimeString();
        $carType->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carType);

        return $carType;
    }

    /**
     * List all items of the To Do list
     *
     * @@param int $modelId
     * @return CarType[]
     */
    public function getCarTypeList($modelId): array
    {
        $result = Collection::make([]);

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarType[] $carTypeList */
        $carTypeList = $database->query(CarType::class)
            ->where('modelId', '=', $modelId)
            ->orderBy('position')
            ->get();

        $collection = Collection::make($carTypeList)->groupBy('position');

        foreach($collection as $position => $entries){

            /** @var CarType[] $carTypeList */
            $carTypeListByPositions = $database->query(CarType::class)
                ->where('modelId', '=', $modelId)
                ->where('position', $position)
                ->orderBy('name')
                ->get();

            foreach($carTypeListByPositions as $carTypeListByPosition) {
                $result->push($carTypeListByPosition);
            }

        }

        return $result->toArray();
    }

    /**
     * Get the item
     *
     * @param int $id
     * @return CarType
     */
    public function getCarType($id): CarType
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarType[] $carTypeList */
        $carTypeList = $database->query(CarType::class)
            ->where('id', '=', $id)
            ->get();

        $carType = $carTypeList[0];
        return $carType;
    }

    /**
     * Update the status of the item
     *
     * @param int $id
     * @param array $data
     * @return CarType
     * @throws ValidationException
     */
    public function updateCarType($id, array $data): CarType
    {
        try {
            CarTypeValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarType[] $carTypeList */
        $carTypeList = $database->query(CarType::class)
            ->where('id', '=', $id)
            ->get();

        $carType = $carTypeList[0];
        $carType->name = $data['name'];
        $carType->position = $data['position'];
        $carType->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carType);

        return $carType;
    }

    public function deleteCarType($id): CarType
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarType[] $carTypeList */
        $carTypeList = $database->query(CarType::class)
            ->where('id', '=', $id)
            ->get();

        /** @var CarType $carType */
        $carType = $carTypeList[0];

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

        return $carType;
    }
}
