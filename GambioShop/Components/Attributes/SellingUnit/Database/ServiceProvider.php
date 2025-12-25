<?php
/*--------------------------------------------------------------------------------------------------
    ServiceProvider.php 2022-09-16
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Attributes\SellingUnit\Database;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Doctrine\DBAL\Connection;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Attributes\SellingUnit\Database\Listener\OnSellingUnitIdCreateListener;
use Gambio\Shop\Attributes\SellingUnit\Database\Repository\Readers\Reader;
use Gambio\Shop\Attributes\SellingUnit\Database\Repository\Readers\ReaderInterface;
use Gambio\Shop\Attributes\SellingUnit\Database\Repository\Repository;
use Gambio\Shop\Attributes\SellingUnit\Database\Repository\RepositoryInterface;
use Gambio\Shop\Attributes\SellingUnit\Database\Service\ReadService;
use Gambio\Shop\Attributes\SellingUnit\Database\Service\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitVpeEvent;
use Gambio\Shop\SellingUnit\Unit\Events\OnSellingUnitIdCreateEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ServiceProvider
 * @package Gambio\Shop\Attributes\SellingUnit\Database
 * @codeCoverageIgnore
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnSellingUnitIdCreateListener::class,
            ReadServiceInterface::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnSellingUnitIdCreateEvent::class, OnSellingUnitIdCreateListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnSellingUnitIdCreateListener::class)
            ->addArgument(ReadServiceInterface::class)
            ->addArgument(EventDispatcherInterface::class);
        $this->application->registerShared(ReadServiceInterface::class, ReadService::class)
            ->addArgument(RepositoryInterface::class);
        $this->application->registerShared(RepositoryInterface::class, Repository::class)
            ->addArgument(ReaderInterface::class);
        $this->application->registerShared(ReaderInterface::class, Reader::class)
            ->addArgument(Connection::class);
    }
}
