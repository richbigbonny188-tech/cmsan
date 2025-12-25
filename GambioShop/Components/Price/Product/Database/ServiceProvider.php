<?php
/**
 * ServiceProvider.php 2020-10-19
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\Shop\Price\Product\Database;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Price\Product\Database\Listener\OnCreateSellingUnitListener;
use Gambio\Shop\Price\Product\Database\ValueObjects\CustomersStatusShowPrice;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnCreateSellingUnitEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Price\Product\Database
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnCreateSellingUnitListener::class,
            CustomersStatusShowPrice::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnCreateSellingUnitEvent::class,
                                          OnCreateSellingUnitListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnCreateSellingUnitListener::class);
        
        $showPriceStatus = isset($_SESSION['customers_status']['customers_status_show_price'])
                           && (int)$_SESSION['customers_status']['customers_status_show_price'] === 1;
        
        $this->application->registerShared(CustomersStatusShowPrice::class)->addArgument($showPriceStatus);
    }
}