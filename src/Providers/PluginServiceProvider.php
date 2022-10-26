<?php

namespace d2gPmPluginCarPartsFinder\Providers;

use d2gPmPluginCarPartsFinder\Contexts\CategoryItemContext;
use d2gPmPluginCarPartsFinder\Contracts\CarBrandRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarModelRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarPlatformRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\CarTypeRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\HSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Contracts\VariationHSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Repositories\CarBrandRepository;
use d2gPmPluginCarPartsFinder\Repositories\CarModelRepository;
use d2gPmPluginCarPartsFinder\Repositories\CarPlatformRepository;
use d2gPmPluginCarPartsFinder\Repositories\CarRepository;
use d2gPmPluginCarPartsFinder\Repositories\CarTypeRepository;
use d2gPmPluginCarPartsFinder\Repositories\HSNTSNRepository;
use d2gPmPluginCarPartsFinder\Repositories\VariationHSNTSNRepository;
use IO\Helper\ResourceContainer;
use IO\Helper\TemplateContainer;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Modules\Webshop\Template\Providers\TemplateServiceProvider;
use Plenty\Plugin\Templates\Twig;

class PluginServiceProvider extends TemplateServiceProvider
{
    const PRIORITY = 0;
    const PLUGIN_NAME = 'd2gPmPluginCarPartsFinder';

    public function register()
    {
        $this->getApplication()->register(PluginRouteServiceProvider::class);
        $this->getApplication()->bind(CarBrandRepositoryContract::class, CarBrandRepository::class);
        $this->getApplication()->bind(CarModelRepositoryContract::class, CarModelRepository::class);
        $this->getApplication()->bind(CarPlatformRepositoryContract::class, CarPlatformRepository::class);
        $this->getApplication()->bind(CarTypeRepositoryContract::class, CarTypeRepository::class);
        $this->getApplication()->bind(CarRepositoryContract::class, CarRepository::class);
        $this->getApplication()->bind(HSNTSNRepositoryContract::class, HSNTSNRepository::class);
        $this->getApplication()->bind(VariationHSNTSNRepositoryContract::class, VariationHSNTSNRepository::class);
            }

    public function boot(Twig $twig, Dispatcher $eventDispatcher)
    {
        $eventDispatcher->listen('IO.Resources.Import', function (ResourceContainer $container){
            $container->addStyleTemplate('d2gPmPluginCarPartsFinder::stylesheet');

            $container->addScriptTemplate('d2gPmPluginCarPartsFinder::script');
            $container->addScriptTemplate('d2gPmPluginCarPartsFinder::Components.CarPartsFinderSearch');
            $container->addScriptTemplate('d2gPmPluginCarPartsFinder::Components.CarPartsFinderCurrent');

            $container->addScriptTemplate('d2gPmPluginCarPartsFinder::Components.CarPartsFinderModal');

            $container->addScriptTemplate('d2gPmPluginCarPartsFinder::modal');


        }, self::PRIORITY);

        $eventDispatcher->listen('IO.ctx.category.item', function (TemplateContainer $templateContainer, $templateData = [])
        {
            $templateContainer->setContext(CategoryItemContext::class);
            return false;
        }, self::PRIORITY);
    }
}
