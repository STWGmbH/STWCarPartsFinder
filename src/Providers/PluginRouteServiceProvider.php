<?php

namespace d2gPmPluginCarPartsFinder\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\ApiRouter;


class PluginRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @param ApiRouter $apiRouter
     */
    public function map(ApiRouter $apiRouter)
    {
        $apiRouter->version(['v1'], ['namespace' => 'd2gPmPluginCarPartsFinder\Controllers', 'middleware' => 'oauth'], function($apiRouter){

            /** @var ApiRouter $apiRouter */
            $apiRouter->post('/car-parts-finder-plugin/csv-import','ImportController@import');
            $apiRouter->post('/car-parts-finder-plugin/clean','ImportController@clean');

            $apiRouter->post('/car-parts-finder-plugin/batch','BatchController@import');
            $apiRouter->post('/car-parts-finder-plugin/batch/items','BatchController@importItemCars');
            $apiRouter->get('/car-parts-finder-plugin/batch/cars','BatchController@indexCars');
            $apiRouter->get('/car-parts-finder-plugin/batch/items','BatchController@indexItems');

            $apiRouter->get('/car-parts-finder-plugin/cars','UiController@indexCars');
            $apiRouter->get('/car-parts-finder-plugin/variations','UiController@indexVariations');
            $apiRouter->get('/car-parts-finder-plugin/variations/{id}','UiController@showVariation');
            $apiRouter->get('/car-parts-finder-plugin/variations-by-category','UiController@indexVariationsByCategory');

            $apiRouter->post('/car-parts-finder-plugin/variations/add-relation','VariationHSNTSNController@store');
            $apiRouter->delete('/car-parts-finder-plugin/variations/delete-relation','VariationHSNTSNController@delete');

            $apiRouter->get('/car-parts-finder-plugin/brands','CarBrandController@index');
            $apiRouter->post('/car-parts-finder-plugin/brands','CarBrandController@store');
            $apiRouter->get('/car-parts-finder-plugin/brands/{id}','CarBrandController@get');
            $apiRouter->put('/car-parts-finder-plugin/brands/{id}','CarBrandController@update');
            $apiRouter->delete('/car-parts-finder-plugin/brands/{id}','CarBrandController@delete');

            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models','CarModelController@index');
            $apiRouter->post('/car-parts-finder-plugin/brands/{brandId}/models','CarModelController@store');
            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{id}','CarModelController@get');
            $apiRouter->put('/car-parts-finder-plugin/brands/{brandId}/models/{id}','CarModelController@update');
            $apiRouter->delete('/car-parts-finder-plugin/brands/{brandId}/models/{id}','CarModelController@delete');

            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types','CarTypeController@index');
            $apiRouter->post('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types','CarTypeController@store');
            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{id}','CarTypeController@get');
            $apiRouter->put('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{id}','CarTypeController@update');
            $apiRouter->delete('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{id}','CarTypeController@delete');

            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms','CarPlatformController@index');
            $apiRouter->post('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms','CarPlatformController@store');
            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{id}','CarPlatformController@get');
            $apiRouter->put('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{id}','CarPlatformController@update');
            $apiRouter->delete('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{id}','CarPlatformController@delete');

            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars','CarController@index');
            $apiRouter->post('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars','CarController@store');
            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{id}','CarController@get');
            $apiRouter->put('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{id}','CarController@update');
            $apiRouter->delete('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{id}','CarController@delete');

            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{carId}/hsntsn','HSNTSNController@index');
            $apiRouter->post('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{carId}/hsntsn','HSNTSNController@store');
            $apiRouter->get('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{carId}/hsntsn/{id}','HSNTSNController@get');
            $apiRouter->put('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{carId}/hsntsn/{id}','HSNTSNController@update');
            $apiRouter->delete('/car-parts-finder-plugin/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{carId}/hsntsn/{id}','HSNTSNController@delete');

        });

        $apiRouter->version(['v1'], ['namespace' => 'd2gPmPluginCarPartsFinder\Controllers'], function($apiRouter){

            $apiRouter->get('/car-parts-finder','CeresController@get');
            $apiRouter->post('/car-parts-finder','CeresController@store');
            $apiRouter->delete('/car-parts-finder','CeresController@delete');

            $apiRouter->get('/car-parts-finder/search','CeresController@search');

            $apiRouter->get('/car-parts-finder/brands','CeresController@indexBrands');
            $apiRouter->get('/car-parts-finder/brands/{brandId}/models','CeresController@indexModels');
            $apiRouter->get('/car-parts-finder/brands/{brandId}/models/{modelId}/types','CeresController@indexTypes');
            $apiRouter->get('/car-parts-finder/brands/{brandId}/models/{modelId}/types/{typeId}/platforms','CeresController@indexPlatforms');
            $apiRouter->get('/car-parts-finder/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars','CeresController@indexCars');
            $apiRouter->get('/car-parts-finder/brands/{brandId}/models/{modelId}/types/{typeId}/platforms/{platformId}/cars/{carId}/hsntsn','CeresController@indexHSNTSN');
        });

        $apiRouter->version(['v1'], ['namespace' => 'd2gPmPluginCarPartsFinder\Helpers', 'middleware' => 'oauth'], function($apiRouter){
            $apiRouter->get('/car-parts-finder-plugin/setBrandsPosition','CarPositionHelper@setBrandsPosition');
            $apiRouter->get('/car-parts-finder-plugin/setModelsPosition','CarPositionHelper@setModelsPosition');
            $apiRouter->get('/car-parts-finder-plugin/setTypesPosition','CarPositionHelper@setTypesPosition');
            $apiRouter->get('/car-parts-finder-plugin/setPlatformsPosition','CarPositionHelper@setPlatformsPosition');
            $apiRouter->get('/car-parts-finder-plugin/setCarsPosition','CarPositionHelper@setCarsPosition');
        });
    }

}
