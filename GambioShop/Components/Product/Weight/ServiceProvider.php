<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2020-11-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Weight;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\Weight\Listener\OnGetProductInfoEventListener;
use Gambio\Shop\Product\Weight\Listener\OnGetSellingUnitWeightEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductInfoEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitWeightEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\Weight
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitWeightEventListener::class,
            OnGetProductInfoEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitWeightEventListener::class);
        $this->application->registerShared(OnGetProductInfoEventListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /**
         * @var EventListenerProvider $listenerProvider
         */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnGetSellingUnitWeightEvent::class,
                                          OnGetSellingUnitWeightEventListener::class);
        $listenerProvider->attachListener(OnGetProductInfoEvent::class, OnGetProductInfoEventListener::class);
    }
}
