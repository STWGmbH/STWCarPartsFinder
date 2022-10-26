<?php

namespace d2gPmPluginCarPartsFinder\Migrations;

use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use d2gPmPluginCarPartsFinder\Models\VariationHSNTSN;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use d2gPmPluginCarPartsFinder\Models\CarBrand;
use d2gPmPluginCarPartsFinder\Models\CarModel;
use d2gPmPluginCarPartsFinder\Models\CarType;
use d2gPmPluginCarPartsFinder\Models\CarPlatform;
use d2gPmPluginCarPartsFinder\Models\Car;

class createBasicTables
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(CarBrand::class);
        $migrate->createTable(CarModel::class);
        $migrate->createTable(CarType::class);
        $migrate->createTable(CarPlatform::class);
        $migrate->createTable(Car::class);
        $migrate->createTable(HSNTSN::class);
        $migrate->createTable(VariationHSNTSN::class);

    }
}
