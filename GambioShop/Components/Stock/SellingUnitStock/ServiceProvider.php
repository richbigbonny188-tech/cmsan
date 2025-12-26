<?php
/*--------------------------------------------------------------------------------------------------
    ServiceProvider.php 2020-10-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Stock\SellingUnitStock;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitStockInfoEvent;
use Gambio\Shop\Stock\SellingUnitStock\Database\Listeners\OnGetSellingUnitStockInfoEventListener;
use League\Container\Container;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @property  Container container
 * @codeCoverageIgnore
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [OnGetSellingUnitStockInfoEventListener::class];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnGetSellingUnitStockInfoEvent::class,
                                          OnGetSellingUnitStockInfoEventListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitStockInfoEventListener::class)
            ->addArgument(EventDispatcherInterface::class);
    }
}