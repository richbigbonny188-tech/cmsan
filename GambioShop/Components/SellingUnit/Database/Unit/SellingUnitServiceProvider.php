<?php
/**
 * SellingUnitServiceProvider.php 2021-01-25
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2021 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\Shop\SellingUnit\Database\Unit;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnCreateSellingUnitEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnSet404HeaderEvent;
use Gambio\Shop\SellingUnit\Database\Unit\Listener\OnCreateSellingUnitListener;
use Gambio\Shop\SellingUnit\Database\Unit\Listener\OnSet404HeaderEventListener;
use Gambio\Shop\SellingUnit\Unit\SellingUnitRepositoryInterface;
use Gambio\Shop\SellingUnit\Unit\Services\Interfaces\SellingUnitReadServiceInterface;
use Gambio\Shop\SellingUnit\Unit\Services\SellingUnitReadService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class SellingUnitServiceProvider
 *
 * @package Gambio\Shop\SellingUnit\Database\Unit
 * @codeCoverageIgnore
 */
class SellingUnitServiceProvider extends AbstractBootableServiceProvider
{
    
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            SellingUnitReadServiceInterface::class,
            OnCreateSellingUnitListener::class,
            OnSet404HeaderEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /**
         * @var EventListenerProvider $listener
         */
        $listener = $this->application->get(EventListenerProvider::class);
        $listener->attachListener(OnCreateSellingUnitEvent::class, OnCreateSellingUnitListener::class);
        $listener->attachListener(OnSet404HeaderEvent::class, OnSet404HeaderEventListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(SellingUnitReadServiceInterface::class, SellingUnitReadService::class)
            ->addArgument(SellingUnitRepositoryInterface::class);
        $this->application->registerShared(SellingUnitRepositoryInterface::class, SellingUnitRepository::class)
            ->addArgument(EventDispatcherInterface::class);
        $this->application->registerShared(OnCreateSellingUnitListener::class)
            ->addArgument(EventDispatcherInterface::class);
        $this->application->registerShared(OnSet404HeaderEventListener::class);
    }
}
