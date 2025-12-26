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

declare(strict_types=1);

namespace Gambio\Shop\Attributes\SellingUnitImages\Database;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces\ReaderDatabaseInterface;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces\ReadRepositoryInterface;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces\ReadServiceInterface;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Listener\OnImageCollectionCreateEventListener;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Repositories\ReaderDatabase;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Repositories\ReadRepository;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Factories\ImageFactory;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Factories\ImageFactoryInterface;
use Gambio\Shop\SellingUnit\Database\Configurations\ShopPaths;
use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEvent;
use Throwable;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Attributes\SellingUnitImages\Database
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnImageCollectionCreateEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnImageCollectionCreateEventListener::class)
            ->addArgument(ReadServiceInterface::class);
        
        $this->application->registerShared(ReadServiceInterface::class, ReadService::class)
            ->addArgument(ReadRepositoryInterface::class);
        
        $this->application->registerShared(ReadRepositoryInterface::class, ReadRepository::class)
            ->addArgument(ReaderDatabaseInterface::class)
            ->addArgument(ImageFactoryInterface::class);
        
        $this->application->registerShared(ReaderDatabaseInterface::class, ReaderDatabase::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(ImageFactoryInterface::class, ImageFactory::class)
            ->addArgument(ShopPaths::class);
        
        $pathBase = '';
        $webBase  = '';
        
        try {
            $pathBase = $this->application->get('path.base');
            $webBase  = $this->application->get('web.base');
        } catch (Throwable $e) {
            /**
             * TODO: This try/catch mus be removed as soon the literals are exported
             */
        }
        
        $this->application->registerShared(ShopPaths::class)->addArgument($pathBase)->addArgument($webBase);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnImageCollectionCreateEvent::class,
                                          OnImageCollectionCreateEventListener::class);
    }
    
    
}