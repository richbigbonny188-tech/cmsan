<?php
/*--------------------------------------------------------------
   ImageListServiceProvider.php 2022-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\AddAnExistingImageToAnImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\CreateImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\DeleteImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\FetchAllImageListsAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\FetchSpecificImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\RemoveImageFromImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\UpdateImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\UpdateImageListImagesAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\UpdateImageListImagesSortOrderAction;
use Gambio\Admin\Modules\ImageList\App\Data\Filter\ImageListFilterFactory;
use Gambio\Admin\Modules\ImageList\App\Data\ImageListMapper;
use Gambio\Admin\Modules\ImageList\App\Data\ImageListReader;
use Gambio\Admin\Modules\ImageList\App\Data\ImageListWriter;
use Gambio\Admin\Modules\ImageList\App\ImageListFilterService;
use Gambio\Admin\Modules\ImageList\App\ImageListReadService;
use Gambio\Admin\Modules\ImageList\App\ImageListRepository;
use Gambio\Admin\Modules\ImageList\App\ImageListWriteService;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\OriginalImagesDirectory;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListFilterService as ImageListFilterServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository as ImageListRepositoryInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestParser;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Url;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ImageListServiceProvider
 *
 * @package Gambio\Admin\Modules\ImageList
 * @codeCoverageIgnore
 */
class ImageListServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ImageListFactory::class,
            ImageListRepositoryInterface::class,
            ImageListFilterServiceInterface::class,
            ImageListWriteServiceInterface::class,
            ImageListReadServiceInterface::class,
            FetchSpecificImageListAction::class,
            RemoveImageFromImageListAction::class,
            DeleteImageListAction::class,
            UpdateImageListImagesSortOrderAction::class,
            CreateImageListAction::class,
            UpdateImageListAction::class,
            AddAnExistingImageToAnImageListAction::class,
            UpdateImageListImagesAction::class,
            FetchAllImageListsAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ImageListFilterFactory::class);
        $this->application->registerShared(OriginalImagesDirectory::class)->addArgument(Path::class);
        $this->application->registerShared(ImageListFactory::class)->addArgument(Path::class)->addArgument(Url::class);
        $this->application->registerShared(ImageListMapper::class)->addArgument(ImageListFactory::class);
        $this->application->registerShared(ImageListReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ImageListWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(OriginalImagesDirectory::class);
        
        $this->application->registerShared(ImageListRepositoryInterface::class, ImageListRepository::class)
            ->addArgument(ImageListMapper::class)
            ->addArgument(ImageListReader::class)
            ->addArgument(ImageListWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(ImageListFilterServiceInterface::class, ImageListFilterService::class)
            ->addArgument(ImageListRepositoryInterface::class)
            ->addArgument(ImageListFilterFactory::class);
        
        $this->application->registerShared(ImageListWriteServiceInterface::class, ImageListWriteService::class)
            ->addArgument(ImageListRepositoryInterface::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(ImageListReadServiceInterface::class, ImageListReadService::class)
            ->addArgument(ImageListRepositoryInterface::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(FetchSpecificImageListAction::class)
            ->addArgument(ImageListReadServiceInterface::class);
        
        $this->application->registerShared(RemoveImageFromImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(DeleteImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class);
        
        $this->application->registerShared(ImageListApiRequestParser::class);
        $this->application->registerShared(ImageListApiRequestValidator::class);
        
        $this->application->registerShared(UpdateImageListImagesSortOrderAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(CreateImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(UpdateImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(AddAnExistingImageToAnImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(UpdateImageListImagesAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(FetchAllImageListsAction::class)
            ->addArgument(ImageListReadServiceInterface::class);
    }
}