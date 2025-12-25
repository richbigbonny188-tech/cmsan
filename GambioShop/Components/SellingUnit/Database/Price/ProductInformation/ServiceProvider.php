<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Price\ProductInformation;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\SellingUnit\Database\Price\ProductInformation\Listener\OnGetSellingUnitPriceEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitPriceEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\SellingUnit\Database\Price\ProductInformation
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitPriceEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitPriceEventListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnGetSellingUnitPriceEvent::class, OnGetSellingUnitPriceEventListener::class);
    }
}