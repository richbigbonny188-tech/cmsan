<?php
/* -----------------------------------------------------------------------------
   ServiceProvider.php 2021-06-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -----------------------------------------------------------------------------
 */

namespace Gambio\Shop\Product\SellingUnitQuantitiy\Database;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\SellingUnitQuantitiy\Criteria\ProductCheckStockCriteria;
use Gambio\Shop\Product\SellingUnitQuantitiy\Database\Listeners\OnGetSelectedQuantityEventListener;
use Gambio\Shop\Product\SellingUnitQuantitiy\Database\Listeners\OnGetSellingUnitAvailableQuantityListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSelectedQuantityEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitAvailableQuantityEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\SellingUnitQuantitiy\Database
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitAvailableQuantityListener::class,
            OnGetSelectedQuantityEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnGetSelectedQuantityEvent::class,
                                          OnGetSelectedQuantityEventListener::class);
        $listenerProvider->attachListener(OnGetSellingUnitAvailableQuantityEvent::class,
                                          OnGetSellingUnitAvailableQuantityListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ProductCheckStockCriteria::class)
            ->addArgument(defined('STOCK_CHECK') ? STOCK_CHECK === 'true' : false)
            ->addArgument(defined('STOCK_ALLOW_CHECKOUT') ? STOCK_ALLOW_CHECKOUT === 'true' : false);
        
        $this->application->registerShared(OnGetSellingUnitAvailableQuantityListener::class)
            ->addArgument(ProductCheckStockCriteria::class)
            ->addArgument(defined('GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CAN_CHECKOUT') ? GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CAN_CHECKOUT : '')
            ->addArgument(defined('GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CANT_CHECKOUT') ? GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CANT_CHECKOUT : '')
            ->addArgument(defined('GM_ORDER_STOCK_CHECKER_NO_STOCK_CANT_CHECKOUT') ? GM_ORDER_STOCK_CHECKER_NO_STOCK_CANT_CHECKOUT : '');
        
        $this->application->registerShared(OnGetSelectedQuantityEventListener::class)
            ->addArgument(defined('MAX_PRODUCTS_QTY') ? MAX_PRODUCTS_QTY : 100000);
    }
}