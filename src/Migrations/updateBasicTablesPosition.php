<?php

namespace d2gPmPluginCarPartsFinder\Migrations;

use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use d2gPmPluginCarPartsFinder\Models\CarBrand;
use d2gPmPluginCarPartsFinder\Models\CarModel;
use d2gPmPluginCarPartsFinder\Models\CarType;
use d2gPmPluginCarPartsFinder\Models\CarPlatform;
use d2gPmPluginCarPartsFinder\Models\Car;

class updateBasicTablesPosition
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->updateTable(CarBrand::class);
        $migrate->updateTable(CarModel::class);
        $migrate->updateTable(CarType::class);
        $migrate->updateTable(CarPlatform::class);
        $migrate->updateTable(Car::class);
    }
}
