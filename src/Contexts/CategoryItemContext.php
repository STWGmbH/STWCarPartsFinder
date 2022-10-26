<?php

namespace d2gPmPluginCarPartsFinder\Contexts;

use Ceres\Contexts\CategoryContext;
use Ceres\Contexts\ItemListContext;
use Ceres\Helper\SearchOptions;
use d2gPmPluginCarPartsFinder\Contracts\VariationHSNTSNRepositoryContract;
use d2gPmPluginCarPartsFinder\Models\Car;
use d2gPmPluginCarPartsFinder\Models\HSNTSN;
use IO\Helper\ContextInterface;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Webshop\ItemSearch\Factories\VariationSearchFactory;
use Plenty\Modules\Webshop\ItemSearch\SearchPresets\CategoryItems;
use Plenty\Modules\Webshop\ItemSearch\SearchPresets\Facets;
use Plenty\Plugin\Log\Loggable;

class CategoryItemContext extends CategoryContext implements ContextInterface
{
    use ItemListContext;
    use Loggable;

    public function init($params)
    {
        parent::init($params);

        $itemListOptions = [
            'page'          => $this->getParam( 'page', 1 ),
            'itemsPerPage'  => $this->getParam( 'itemsPerPage', '' ),
            'sorting'       => $this->getParam( 'sorting', '' ),
            'facets'        => $this->getParam( 'facets' ),
            'categoryId'    => $this->category->id,
            'priceMin'      => $this->request->get('priceMin', 0),
            'priceMax'      => $this->request->get('priceMax', 0)
        ];

        /** @var FrontendSessionStorageFactoryContract $sessionStorage */
        $sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);

        /** @var HSNTSN $hsntsn */
        $hsntsn = $sessionStorage->getPlugin()->getValue('CAR_PARTS_FILTER_HSNTSN');

        $this->getLogger("CategoryItemContext")->debug('d2gPmPluginCarPartsFinder::CategoryItemContext.run', ['hsntsn' => $hsntsn]);


        if(!empty($hsntsn)){

            /** @var VariationHSNTSNRepositoryContract $variationHSNTSNRepositoryContract */
            $variationHSNTSNRepositoryContract = pluginApp(VariationHSNTSNRepositoryContract::class);
            $variationIds = $variationHSNTSNRepositoryContract->indexByCategory($hsntsn->id, $this->category->id);
        }

        $itemListOptions = SearchOptions::validateItemListOptions($itemListOptions, SearchOptions::SCOPE_CATEGORY);

        /** @var VariationSearchFactory $itemList */
        $itemList = CategoryItems::getSearchFactory( $itemListOptions );

        if(!empty($hsntsn)){
            if(count($variationIds) > 0){
                $itemList->hasVariationIds($variationIds);
            } else {
                $itemList->hasVariationIds([0]);
            }
        }

        $this->initItemList(
            [
                'itemList' => $itemList,
                'facets'   => Facets::getSearchFactory( $itemListOptions )
            ],
            $itemListOptions,
            SearchOptions::SCOPE_CATEGORY
        );
    }
}
