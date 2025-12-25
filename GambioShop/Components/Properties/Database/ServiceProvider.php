<?php
/*------------------------------------------------------------------------------
 ServiceProvider.php 2022-09-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Shop\Properties\Database;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Shop\Product\Model\Criteria\ShowModelCriteria;
use Gambio\Shop\Properties\Database\Criterias\CheckStockBeforeShoppingCartCriteria;
use Gambio\Shop\Properties\Database\Criterias\CheckStockCriteria;
use Gambio\Shop\Properties\Database\Listeners\OnGetSellingUnitAvailableQuantityListener;
use Gambio\Shop\Properties\Database\Listeners\OnGetSellingUnitEanEventListener;
use Gambio\Shop\Properties\Database\Listeners\OnGetSellingUnitModelEventListener;
use Gambio\Shop\Properties\Database\Listeners\OnGetSellingUnitPriceEventListener;
use Gambio\Shop\Properties\Database\Listeners\OnGetSellingUnitVpeEventListener;
use Gambio\Shop\Properties\Database\Listeners\OnGetSellingUnitWeightEventListener;
use Gambio\Shop\Properties\Database\Listeners\OnGetShippingInfoEventListener;
use Gambio\Shop\Properties\Database\Readers\CachedPropertyReader;
use Gambio\Shop\Properties\Database\Readers\Interfaces\PropertyReaderInterface;
use Gambio\Shop\Properties\Database\Repositories\Interfaces\PropertyReadRepositoryInterface;
use Gambio\Shop\Properties\Database\Repositories\PropertyReadRepository;
use Gambio\Shop\Properties\Database\Services\Interfaces\PropertiesReaderServiceInterface;
use Gambio\Shop\Properties\Database\Services\Interfaces\PropertyQuantityReadServiceInterface;
use Gambio\Shop\Properties\Database\Services\PropertiesReaderService;
use Gambio\Shop\Properties\Database\Services\PropertyQuantityReadService;
use Gambio\Shop\Properties\Properties\Builders\CombinationBuilder;
use Gambio\Shop\Properties\Properties\Builders\CombinationBuilderInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitAvailableQuantityEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitEanEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitModelEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitPriceEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitVpeEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitWeightEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetShippingInfoEvent;

/**
 * Class ServiceProvider
 *
 * @package            Gambio\Shop\Properties\Database
 * @codeCoverageIgnore providers dont need to be tested
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            PropertiesReaderServiceInterface::class,
            CheckStockBeforeShoppingCartCriteria::class,
            OnGetSellingUnitAvailableQuantityListener::class,
            OnGetSellingUnitWeightEventListener::class,
            OnGetSellingUnitEanEventListener::class,
            OnGetSellingUnitPriceEventListener::class,
            OnGetSellingUnitModelEventListener::class,
            OnGetSellingUnitVpeEventListener::class,
            OnGetShippingInfoEventListener::class,
            CheckStockCriteria::class,
            PropertyReadRepositoryInterface::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CheckStockBeforeShoppingCartCriteria::class)
            ->addArgument(defined('STOCK_ALLOW_CHECKOUT') ? STOCK_ALLOW_CHECKOUT === 'true' : false)
            ->addArgument(defined('STOCK_CHECK') ? STOCK_CHECK === 'true' : false)
            ->addArgument(defined('CHECK_STOCK_BEFORE_SHOPPING_CART') ? CHECK_STOCK_BEFORE_SHOPPING_CART
                                                                        === 'true' : false)
            ->addArgument(defined('ATTRIBUTE_STOCK_CHECK') ? ATTRIBUTE_STOCK_CHECK === 'true' : false);
        
        $this->application->registerShared(CheckStockCriteria::class)
            ->addArgument(defined('STOCK_CHECK') ? STOCK_CHECK === 'true' : false)
            ->addArgument(defined('ATTRIBUTE_STOCK_CHECK') ? ATTRIBUTE_STOCK_CHECK === 'true' : false)
            ->addArgument(defined('STOCK_ALLOW_CHECKOUT') ? STOCK_ALLOW_CHECKOUT === 'true' : false);
        
        $this->application->registerShared(PropertyQuantityReadServiceInterface::class,
                                           PropertyQuantityReadService::class)
            ->addArgument(PropertyReadRepositoryInterface::class)
            ->addArgument(CheckStockCriteria::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(OnGetSellingUnitAvailableQuantityListener::class)
            ->addArgument(PropertyQuantityReadServiceInterface::class)
            ->addArgument(PropertyQuantityReadService::class);
        
        $this->application->registerShared(CombinationBuilderInterface::class, CombinationBuilder::class);
        
        $this->application->registerShared(PropertiesReaderServiceInterface::class, PropertiesReaderService::class)
            ->addArgument(PropertyReadRepositoryInterface::class);
        
        $this->application->registerShared(OnGetSellingUnitEanEventListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class);
        
        $this->application->registerShared(OnGetShippingInfoEventListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class);
        
        $this->application->registerShared(PropertyReadRepositoryInterface::class, PropertyReadRepository::class)
            ->addArgument(PropertyReaderInterface::class);
        
        $this->application->registerShared(PropertyReaderInterface::class, CachedPropertyReader::class)
            ->addArgument(Connection::class)
            ->addArgument(CombinationBuilderInterface::class)
            ->addArgument(CheckStockBeforeShoppingCartCriteria::class)
            ->addArgument(CheckStockCriteria::class);
        
        $this->application->registerShared(PropertiesReaderServiceInterface::class, PropertiesReaderService::class);
        $this->application->registerShared(PropertiesReaderService::class);
        $this->application->registerShared(OnGetSellingUnitWeightEventListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class);
        
        $this->application->registerShared(OnGetSellingUnitPriceEventListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class);
        
        $configuration                 = $this->application->get(ConfigurationService::class);
        $showInShoppingCartAndWishlist = $configuration->find('gm_configuration/SHOW_PRODUCTS_MODEL_IN_SHOPPING_CART_AND_WISHLIST');
        $showInProductLists            = $configuration->find('gm_configuration/SHOW_PRODUCTS_MODEL_IN_PRODUCT_LISTS');
        $showInProductDetails          = $configuration->find('gm_configuration/SHOW_PRODUCTS_MODEL_IN_PRODUCT_DETAILS');
        
        $this->application->registerShared(ShowModelCriteria::class)
            ->addArgument($showInShoppingCartAndWishlist
                          && $showInShoppingCartAndWishlist->value() === 'true')
            ->addArgument($showInProductLists && $showInProductLists->value() === 'true')
            ->addArgument($showInProductDetails && $showInProductDetails->value() === 'true');
        
        $this->application->registerShared(OnGetSellingUnitModelEventListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class)
            ->addArgument($this->appendPropertiesModel())
            ->addArgument(ShowModelCriteria::class);
        
        $this->application->registerShared(OnGetSellingUnitVpeEventListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class);
    }
    
    
    protected function appendPropertiesModel(): bool
    {
        return defined('APPEND_PROPERTIES_MODEL') ? APPEND_PROPERTIES_MODEL === 'true' : false;
    }
    
    
    public function boot(): void
    {
        $this->application->attachEventListener(OnGetSellingUnitWeightEvent::class,
                                                OnGetSellingUnitWeightEventListener::class);
        $this->application->attachEventListener(OnGetSellingUnitPriceEvent::class,
                                                OnGetSellingUnitPriceEventListener::class);
        $this->application->attachEventListener(OnGetSellingUnitModelEvent::class,
                                                OnGetSellingUnitModelEventListener::class);
        $this->application->attachEventListener(OnGetSellingUnitVpeEvent::class,
                                                OnGetSellingUnitVpeEventListener::class);
        $this->application->attachEventListener(OnGetShippingInfoEvent::class, OnGetShippingInfoEventListener::class);
        
        $this->application->attachPrioritisedEventListener(OnGetSellingUnitEanEvent::class,
                                                           OnGetSellingUnitEanEventListener::class);
        $this->application->attachPrioritisedEventListener(OnGetSellingUnitAvailableQuantityEvent::class,
                                                           OnGetSellingUnitAvailableQuantityListener::class);
    }
}
