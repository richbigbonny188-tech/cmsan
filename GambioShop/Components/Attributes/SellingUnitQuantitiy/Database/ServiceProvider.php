<?php
/*--------------------------------------------------------------------------------------------------
    ServiceProvider.php 2021-03-01
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Attributes\SellingUnitQuantitiy\Database;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\PrioritizingEventListenerProvider;
use Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Listeners\OnGetSellingUnitAvailableQuantityListener;
use Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Repository\Reader\Reader;
use Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Repository\Reader\ReaderInterface;
use Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Repository\Repository;
use Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Repository\RepositoryInterface;
use Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Services\ReaderService;
use Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\Services\ReaderServiceInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitAvailableQuantityEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Attributes\SellingUnitQuantitiy\Database
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
            OnGetSellingUnitAvailableQuantityListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var PrioritizingEventListenerProvider $prioritizingListenerProvider */
        $prioritizingListenerProvider = $this->application->get(PrioritizingEventListenerProvider::class);
        $prioritizingListenerProvider->attachListener(OnGetSellingUnitAvailableQuantityEvent::class,
                                                      OnGetSellingUnitAvailableQuantityListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitAvailableQuantityListener::class)
            ->addArgument(ReaderServiceInterface::class)
            ->addArgument(defined('STOCK_ALLOW_CHECKOUT') ? STOCK_ALLOW_CHECKOUT === 'true' : false)
            ->addArgument(defined('GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CAN_CHECKOUT') ? GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CAN_CHECKOUT : '')
            ->addArgument(defined('GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CANT_CHECKOUT') ? GM_ORDER_STOCK_CHECKER_OUT_OF_STOCK_CANT_CHECKOUT : '')
            ->addArgument(defined('GM_ORDER_STOCK_CHECKER_NO_STOCK_CANT_CHECKOUT') ? GM_ORDER_STOCK_CHECKER_NO_STOCK_CANT_CHECKOUT : '');
        
        $this->application->registerShared(ReaderServiceInterface::class, ReaderService::class)
            ->addArgument(RepositoryInterface::class)
            ->addArgument(defined('STOCK_CHECK') ? STOCK_CHECK === 'true' : false)
            ->addArgument(defined('ATTRIBUTE_STOCK_CHECK') ? ATTRIBUTE_STOCK_CHECK === 'true' : false)
            ->addArgument(defined('DOWNLOAD_STOCK_CHECK') ? DOWNLOAD_STOCK_CHECK === 'true' : false);
        $this->application->registerShared(RepositoryInterface::class, Repository::class)->addArgument(ReaderInterface::class);
        $this->application->registerShared(ReaderInterface::class, Reader::class)->addArgument(Connection::class);
    }
}