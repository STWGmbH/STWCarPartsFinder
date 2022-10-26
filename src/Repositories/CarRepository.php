<?php

namespace d2gPmPluginCarPartsFinder\Repositories;

use Carbon\Carbon;
use d2gPmPluginCarPartsFinder\Contracts\CarRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use d2gPmPluginCarPartsFinder\Validators\CarValidator;
use Illuminate\Support\Collection;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Log\Loggable;

class CarRepository implements CarRepositoryContract
{
    use Loggable;
    /**
     * Add a new brand
     *
     * @param int $platformId
     * @param array $data
     * @return Car
     * @throws ValidationException
     */

    public function firstOrCreate($platformId, array $data):Car
    {
        try {
            CarValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var Car[] $carList */
        $carList = $database->query(Car::class)
            ->where('platformId', '=', $platformId)
            ->where('name', '=', $data['name'])
            ->get();

        if(!empty($carList)){
            return $carList[0];
        }

        /** @var Car $car */
        $car = pluginApp(Car::class);
        $car->platformId = $platformId;
        $car->name = $data['name'];
        $car->ktype = $data['ktype'];
        $car->createdAt = Carbon::now()->toDateTimeString();
        $car->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($car);

        return $car;
    }

    /**
     * Add a new brand
     *
     * @param int $platformId
     * @param array $data
     * @return Car
     * @throws ValidationException
     */

    public function createCar($platformId, array $data):Car
    {
        try {
            CarValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var Car $car */
        $car = pluginApp(Car::class);
        $car->platformId = $platformId;
        $car->name = $data['name'];
        $car->ktype = $data['ktype'];
        $car->position = $data['position'];
        $car->createdAt = Carbon::now()->toDateTimeString();
        $car->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($car);

        return $car;
    }

    /**
     * List all items of the To Do list
     *
     * @@param int $platformId
     * @return Car[]
     */
    public function getCarList($platformId): array
    {
        $result = Collection::make([]);

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var Car[] $carList */
        $carList = $database->query(Car::class)
            ->where('platformId', '=', $platformId)
            ->orderBy('position')
            ->get();

        $collection = Collection::make($carList)->groupBy('position');

        foreach($collection as $position => $entries){

            /** @var CarPlatform[] $carPlatformList */
            $carListByPositions = $database->query(Car::class)
                ->where('platformId', '=', $platformId)
                ->where('position', $position)
                ->orderBy('name')
                ->get();

            foreach($carListByPositions as $carListByPosition) {
                $result->push($carListByPosition);
            }

        }

        return $result->toArray();
    }

    /**
     * Get the item
     *
     * @param int $id
     * @return Car
     */
    public function getCar($id): Car
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var Car[] $carList */
        $carList = $database->query(Car::class)
            ->where('id', '=', $id)
            ->get();

        $car = $carList[0];
        return $car;
    }

    /**
     * Update the status of the item
     *
     * @param int $id
     * @param array $data
     * @return Car
     * @throws ValidationException
     */
    public function updateCar($id, array $data): Car
    {
        try {
            CarValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var Car[] $carList */
        $carList = $database->query(Car::class)
            ->where('id', '=', $id)
            ->get();

        $car = $carList[0];
        $car->name = $data['name'];
        $car->ktype = $data['ktype'];
        $car->position = $data['position'];
        $car->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($car);

        return $car;
    }

    public function deleteCar($id): Car
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var Car[] $carList */
        $carList = $database->query(Car::class)
            ->where('id', '=', $id)
            ->get();

        /** @var Car $car */
        $car = $carList[0];

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

        return $car;
    }
}
