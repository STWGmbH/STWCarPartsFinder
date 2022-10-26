<?php

namespace d2gPmPluginCarPartsFinder\Repositories;

use Carbon\Carbon;
use d2gPmPluginCarPartsFinder\Contracts\CarPlatformRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\CarPlatform;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use d2gPmPluginCarPartsFinder\Validators\CarPlatformValidator;
use Illuminate\Support\Collection;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class CarPlatformRepository implements CarPlatformRepositoryContract
{

    /**
     * Add a new platform
     *
     * @param int $typeId
     * @param array $data
     * @return CarPlatform
     * @throws ValidationException
     */

    public function firstOrCreate($typeId, array $data):CarPlatform
    {
        try {
            CarPlatformValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarPlatform[] $carPlatformList */
        $carPlatformList = $database->query(CarPlatform::class)
            ->where('typeId', '=', $typeId)
            ->where('name', '=', $data['name'])
            ->get();

        if(!empty($carPlatformList)){
            return $carPlatformList[0];
        }

        /** @var CarPlatform $carPlatform */
        $carPlatform = pluginApp(CarPlatform::class);
        $carPlatform->typeId = $typeId;
        $carPlatform->name = $data['name'];
        $carPlatform->createdAt = Carbon::now()->toDateTimeString();
        $carPlatform->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carPlatform);

        return $carPlatform;
    }

    /**
     * Add a new platform
     *
     * @param int $typeId
     * @param array $data
     * @return CarPlatform
     * @throws ValidationException
     */

    public function createCarPlatform($typeId, array $data):CarPlatform
    {
        try {
            CarPlatformValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarPlatform $carPlatform */
        $carPlatform = pluginApp(CarPlatform::class);
        $carPlatform->typeId = $typeId;
        $carPlatform->name = $data['name'];
        $carPlatform->position = $data['position'];
        $carPlatform->createdAt = Carbon::now()->toDateTimeString();
        $carPlatform->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carPlatform);

        return $carPlatform;
    }

    /**
     * List all platforms of the Type
     *
     * @@param int $typeId
     * @return CarPlatform[]
     */
    public function getCarPlatformList($typeId): array
    {
        $result = Collection::make([]);

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarPlatform[] $carPlatformList */
        $carPlatformList = $database->query(CarPlatform::class)
            ->where('typeId', '=', $typeId)
            ->orderBy('position')
            ->get();

        $collection = Collection::make($carPlatformList)->groupBy('position');

        foreach($collection as $position => $entries){

            /** @var CarPlatform[] $carPlatformList */
            $carPlatformListByPositions = $database->query(CarPlatform::class)
                ->where('typeId', '=', $typeId)
                ->where('position', $position)
                ->orderBy('name')
                ->get();

            foreach($carPlatformListByPositions as $carPlatformListByPosition) {
                $result->push($carPlatformListByPosition);
            }

        }

        return $result->toArray();
    }

    /**
     * Get the item
     *
     * @param int $id
     * @return CarPlatform
     */
    public function getCarPlatform($id): CarPlatform
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarPlatform[] $carPlatformList */
        $carPlatformList = $database->query(CarPlatform::class)
            ->where('id', '=', $id)
            ->get();

        $carPlatform = $carPlatformList[0];
        return $carPlatform;
    }

    /**
     * Update the item
     *
     * @param int $id
     * @param array $data
     * @return CarPlatform
     * @throws ValidationException
     */
    public function updateCarPlatform($id, array $data): CarPlatform
    {
        try {
            CarPlatformValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarPlatform[] $carPlatformList */
        $carPlatformList = $database->query(CarPlatform::class)
            ->where('id', '=', $id)
            ->get();

        $carPlatform = $carPlatformList[0];
        $carPlatform->name = $data['name'];
        $carPlatform->position = $data['position'];
        $carPlatform->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($carPlatform);

        return $carPlatform;
    }

    public function deleteCarPlatform($id): CarPlatform
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var CarPlatform[] $carPlatformList */
        $carPlatformList = $database->query(CarPlatform::class)
            ->where('id', '=', $id)
            ->get();

        /** @var CarPlatform $carPlatform */
        $carPlatform = $carPlatformList[0];

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

        return $carPlatform;
    }
}
