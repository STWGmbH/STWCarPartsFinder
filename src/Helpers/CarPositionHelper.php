<?php

namespace d2gPmPluginCarPartsFinder\Helpers;

use d2gPmPluginCarPartsFinder\Contracts\CarBrandRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarModelRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarPlatformRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarTypeRepositoryContract;
use Plenty\Plugin\Http\Request;

class CarPositionHelper
{
    /**
     * @var CarBrandRepositoryContract
     */
    private $carBrandRepositoryContract;

    /**
     * @var CarModelRepositoryContract
     */
    private $carModelRepositoryContract;

    /**
     * @var CarTypeRepositoryContract
     */
    private $carTypeRepositoryContract;

    /**
     * @var CarPlatformRepositoryContract
     */
    private $carPlatformRepositoryContract;

    /**
     * @var CarRepositoryContract
     */
    private $carRepositoryContract;

    /**
     * @param CarBrandRepositoryContract $carBrandRepositoryContract
     * @param CarModelRepositoryContract $carModelRepositoryContract
     * @param CarTypeRepositoryContract $carTypeRepositoryContract
     * @param CarPlatformRepositoryContract $carPlatformRepositoryContract
     * @param CarRepositoryContract $carRepositoryContract
     */
    public function __construct(CarBrandRepositoryContract $carBrandRepositoryContract,
                                CarModelRepositoryContract $carModelRepositoryContract,
                                CarTypeRepositoryContract $carTypeRepositoryContract,
                                CarPlatformRepositoryContract $carPlatformRepositoryContract,
                                CarRepositoryContract $carRepositoryContract)
    {
        $this->carBrandRepositoryContract = $carBrandRepositoryContract;
        $this->carModelRepositoryContract = $carModelRepositoryContract;
        $this->carTypeRepositoryContract = $carTypeRepositoryContract;
        $this->carPlatformRepositoryContract = $carPlatformRepositoryContract;
        $this->carRepositoryContract = $carRepositoryContract;
    }

    /**
     * @return string
     */
    public function setBrandsPosition()
    {

        $brandArray = [];

        $brands = $this->carBrandRepositoryContract->getCarBrandList();

        foreach ($brands as $brand) {
            $this->carBrandRepositoryContract->updateCarBrand($brand->id,
                [
                 'position' => 100,
                 'name' => $brand->name,
                ]);

            array_push($brandArray, $brand);
        }

        return json_encode([
            'totalsCount' => count($brandArray),
            'brands' => $brandArray
        ]);
    }

    /**
     * @return string
     */
    public function setModelsPosition()
    {
        $modelArray = [];
        $brands = $this->carBrandRepositoryContract->getCarBrandList();

        foreach ($brands as $brand) {
            $models = $this->carModelRepositoryContract->getCarModelList($brand->id);
            foreach ($models as $model) {
                if(is_null($model->position)) {
                    $this->carModelRepositoryContract->updateCarModel($model->id,
                        [
                            'position' => 100,
                            'name' => $model->name,
                        ]);

                    array_push($modelArray, $model);
                }
            }
        }

        return json_encode([
            'totalsCount' => count($modelArray),
            'models' => $modelArray
        ]);
    }

    /**
     * @return string
     */
    public function setTypesPosition()
    {
        $typeArray = [];
        $brands = $this->carBrandRepositoryContract->getCarBrandList();

        foreach ($brands as $brand) {
            $models = $this->carModelRepositoryContract->getCarModelList($brand->id);
            foreach ($models as $model) {
                $types = $this->carTypeRepositoryContract->getCarTypeList($model->id);
                foreach ($types as $type) {
                    $this->carTypeRepositoryContract->updateCarType($type->id,
                    [
                        'position' => 100,
                        'name' => $type->name,
                    ]);

                    array_push($typeArray, $model);
                }
            }
        }

        return json_encode([
            'totalsCount' => count($typeArray),
            'models' => $typeArray
        ]);
    }

    /**
     * @return string
     */
    public function setPlatformsPosition()
    {
        $platformArray = [];
        $brands = $this->carBrandRepositoryContract->getCarBrandList();

        foreach ($brands as $brand) {
            $models = $this->carModelRepositoryContract->getCarModelList($brand->id);
            foreach ($models as $model) {
                $types = $this->carTypeRepositoryContract->getCarTypeList($model->id);
                foreach ($types as $type) {
                    $platforms = $this->carPlatformRepositoryContract->getCarPlatformList($type->id);
                    foreach ($platforms as $platform) {
                        $this->carPlatformRepositoryContract->updateCarPlatform($platform->id,
                            [
                                'position' => 100,
                                'name' => $platform->name,
                            ]);

                        array_push($platformArray, $model);
                    }
                }
            }
        }

        return json_encode([
            'totalsCount' => count($platformArray),
            'models' => $platformArray
        ]);
    }

    /**
     * @return string
     */
    public function setCarsPosition()
    {
        $carArray = [];
        $brands = $this->carBrandRepositoryContract->getCarBrandList();

        foreach ($brands as $brand) {
            $models = $this->carModelRepositoryContract->getCarModelList($brand->id);
            foreach ($models as $model) {
                $types = $this->carTypeRepositoryContract->getCarTypeList($model->id);
                foreach ($types as $type) {
                    $platforms = $this->carPlatformRepositoryContract->getCarPlatformList($type->id);
                    foreach ($platforms as $platform) {
                        $cars = $this->carRepositoryContract->getCarList($platform->id);
                        foreach ($cars as $car) {
                            $this->carRepositoryContract->updateCar($car->id,
                                [
                                    'position' => 100,
                                    'name' => $car->name,
                                ]);

                            array_push($carArray, $model);
                        }
                    }
                }
            }
        }

        return json_encode([
            'totalsCount' => count($carArray),
            'models' => $carArray
        ]);
    }
}
