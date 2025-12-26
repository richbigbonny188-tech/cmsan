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

namespace Gambio\Shop\Product\SellingUnitImage\Database;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Core\Filesystem\Interfaces\Filesystem;
use Gambio\Core\Images\ValueObjects\ProductGalleryImages;
use Gambio\Core\Images\ValueObjects\ProductInfoImages;
use Gambio\Core\Images\ValueObjects\ProductOriginalImages;
use Gambio\Core\Images\ValueObjects\ProductPopUpImages;
use Gambio\Core\Images\ValueObjects\ProductThumbnailImages;
use Gambio\Shop\Product\SellingUnitImage\Database\Listerner\OnImageCollectionCreateEventListener;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\ImageDtoBuilder;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\Interfaces\ImageDtoBuilderInterface;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Factories\ImageFactory;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Factories\ImageFactoryInterface;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Readers\Reader;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Readers\ReaderInterface;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Repository;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\RepositoryInterface;
use Gambio\Shop\Product\SellingUnitImage\Database\Service\ReadService;
use Gambio\Shop\Product\SellingUnitImage\Database\Service\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\SellingUnitImage\Database
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnImageCollectionCreateEventListener::class,
            ReadServiceInterface::class
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
            ->addArgument(RepositoryInterface::class);
        
        $this->application->registerShared(RepositoryInterface::class, Repository::class)
            ->addArgument(ReaderInterface::class)
            ->addArgument(ImageFactoryInterface::class);
        
        $this->application->registerShared(ReaderInterface::class, Reader::class)
            ->addArgument(Connection::class)
            ->addArgument(Filesystem::class)
            ->addArgument(ImageDtoBuilderInterface::class)
            ->addArgument(ProductOriginalImages::class)
            ->addArgument(ProductInfoImages::class)
            ->addArgument(ProductPopUpImages::class)
            ->addArgument(ProductThumbnailImages::class)
            ->addArgument(ProductGalleryImages::class);
        $this->application->registerShared(ImageDtoBuilderInterface::class, ImageDtoBuilder::class);
        $this->application->registerShared(ImageFactoryInterface::class, ImageFactory::class);
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
        $listener->attachListener(OnImageCollectionCreateEvent::class, OnImageCollectionCreateEventListener::class);
    }
}