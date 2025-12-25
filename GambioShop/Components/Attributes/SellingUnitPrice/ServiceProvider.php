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

namespace Gambio\Shop\Attributes\SellingUnitPrice;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Attributes\SellingUnitPrice\Listener\OnGetSellingUnitPriceEventListener;
use Gambio\Shop\Attributes\SellingUnitPrice\Repository\Readers\Reader;
use Gambio\Shop\Attributes\SellingUnitPrice\Repository\Readers\ReaderInterface;
use Gambio\Shop\Attributes\SellingUnitPrice\Repository\Repository;
use Gambio\Shop\Attributes\SellingUnitPrice\Repository\RepositoryInterface;
use Gambio\Shop\Attributes\SellingUnitPrice\Service\ReadService;
use Gambio\Shop\Attributes\SellingUnitPrice\Service\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitPriceEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Attributes\SellingUnitPrice
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
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
        $this->application->registerShared(OnGetSellingUnitPriceEventListener::class)
            ->addArgument(ReadServiceInterface::class);
        
        $this->application->registerShared(ReadServiceInterface::class, ReadService::class)
            ->addArgument(RepositoryInterface::class);
        
        $this->application->registerShared(RepositoryInterface::class, Repository::class)
            ->addArgument(ReaderInterface::class);
        
        $this->application->registerShared(ReaderInterface::class, Reader::class)->addArgument(Connection::class);
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
        $listenerProvider->attachListener(OnGetSellingUnitPriceEvent::class, OnGetSellingUnitPriceEventListener::class);
    }
}