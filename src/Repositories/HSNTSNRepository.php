<?php

namespace d2gPmPluginCarPartsFinder\Repositories;

use Carbon\Carbon;
use d2gPmPluginCarPartsFinder\Contracts\HSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use d2gPmPluginCarPartsFinder\Validators\HSNTSNValidator;
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class HSNTSNRepository implements HSNTSNRepositoryContract
{

    /**
     * Add a new hsn/tsn
     *
     * @param int $carId
     * @param array $data
     * @return HSNTSN
     * @throws ValidationException
     */

    public function firstOrCreate($carId, array $data):HSNTSN
    {
        try {
            HSNTSNValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var HSNTSN[] $list */
        $list = $database->query(HSNTSN::class)
            ->where('carId', '=', $carId)
            ->where('hsn', '=', $data['hsn'])
            ->where('tsn', '=', $data['tsn'])
            ->get();

        if(!empty($list)){
            return $list[0];
        }

        /** @var HSNTSN $entry */
        $entry = pluginApp(HSNTSN::class);
        $entry->carId = $carId;
        $entry->hsn = $data['hsn'];
        $entry->tsn = $data['tsn'];
        $entry->createdAt = Carbon::now()->toDateTimeString();
        $entry->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($entry);

        return $entry;
    }

    /**
     * Add a new hsn/tsn
     *
     * @param int $carId
     * @param array $data
     * @return HSNTSN
     * @throws ValidationException
     */

    public function create($carId, array $data):HSNTSN
    {
        try {
            HSNTSNValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var HSNTSN $entry */
        $entry = pluginApp(HSNTSN::class);
        $entry->carId = $carId;
        $entry->hsn = $data['hsn'];
        $entry->tsn = $data['tsn'];
        $entry->createdAt = Carbon::now()->toDateTimeString();
        $entry->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($entry);

        return $entry;
    }

    /**
     * List all items of the To Do list
     *
     * @@param int $carId
     * @return HSNTSN[]
     */
    public function index($carId): array
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var HSNTSN[] $list */
        $list = $database->query(HSNTSN::class)
            ->where('carId', '=', $carId)
            ->get();
        return $list;
    }

    /**
     * Get the item
     *
     * @param int $id
     * @return HSNTSN
     */
    public function get($id): HSNTSN
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var HSNTSN[] $list */
        $list = $database->query(HSNTSN::class)
            ->where('id', '=', $id)
            ->get();

        $entry = $list[0];
        return $entry;
    }

    /**
     * Update the status of the item
     *
     * @param int $id
     * @param array $data
     * @return HSNTSN
     * @throws ValidationException
     */
    public function update($id, array $data): HSNTSN
    {
        try {
            HSNTSNValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var HSNTSN[] $list */
        $list = $database->query(HSNTSN::class)
            ->where('id', '=', $id)
            ->get();

        $entry = $list[0];
        $entry->hsn = $data['hsn'];
        $entry->tsn = $data['tsn'];
        $entry->updatedAt = Carbon::now()->toDateTimeString();
        $database->save($entry);

        return $entry;
    }

    public function delete($id): HSNTSN
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);

        /** @var HSNTSN[] $list */
        $list = $database->query(HSNTSN::class)
            ->where('id', '=', $id)
            ->get();

        /** @var VariationHSNTSN[] $list */
        $variationHsnTsnlist = $database->query(VariationHSNTSN::class)
            ->where('hsntsnId', '=', $id)
            ->get();

        foreach($variationHsnTsnlist as $item){
            $database->delete($item);
        }

        $entry = $list[0];
        $database->delete($entry);

        return $entry;
    }
}
