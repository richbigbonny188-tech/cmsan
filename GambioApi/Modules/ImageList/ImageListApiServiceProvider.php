<?php
/*--------------------------------------------------------------
   ImageListApiServiceProvider.php 2022-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList;

use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListFilterService as ImageListFilterServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Api\Modules\ImageList\App\Actions\AddANewImageToAnImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\AddAnExistingImageToAnImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\CreateImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\DeleteImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\FetchAllImageListsAction;
use Gambio\Api\Modules\ImageList\App\Actions\FetchImagesFromASpecificImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\FetchSpecificImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\RemoveImageFromImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\UpdateImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\UpdateImageListImagesAction;
use Gambio\Api\Modules\ImageList\App\Actions\UpdateImageListImagesSortOrderAction;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestParser;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class ImageListApiServiceProvider
 *
 * @package Gambio\Api\Modules\Option
 * @codeCoverageIgnore
 */
class ImageListApiServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AddANewImageToAnImageListAction::class,
            AddAnExistingImageToAnImageListAction::class,
            CreateImageListAction::class,
            DeleteImageListAction::class,
            FetchAllImageListsAction::class,
            FetchSpecificImageListAction::class,
            RemoveImageFromImageListAction::class,
            UpdateImageListAction::class,
            UpdateImageListImagesAction::class,
            UpdateImageListImagesSortOrderAction::class,
            FetchImagesFromASpecificImageListAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ImageListApiRequestParser::class);
        $this->application->registerShared(ImageListApiRequestValidator::class);
        
        $this->application->registerShared(FetchAllImageListsAction::class)
            ->addArgument(ImageListApiRequestParser::class)
            ->addArgument(ImageListFilterServiceInterface::class);
        
        $this->application->registerShared(FetchSpecificImageListAction::class)
            ->addArgument(ImageListReadServiceInterface::class);
        
        $this->application->registerShared(FetchImagesFromASpecificImageListAction::class)
            ->addArgument(ImageListReadServiceInterface::class);
        
        $this->application->registerShared(DeleteImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class);
        
        $this->application->registerShared(UpdateImageListImagesSortOrderAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(UpdateImageListImagesAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(AddAnExistingImageToAnImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(UpdateImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListFactory::class);
        
        $this->application->registerShared(AddANewImageToAnImageListAction::class)
            ->addArgument(ImageListFactory::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(LanguageService::class);
        
        $this->application->registerShared(CreateImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListApiRequestValidator::class)
            ->addArgument(ImageListApiRequestParser::class)
            ->addArgument(ImageListFactory::class)
            ->addArgument(Url::class);
        
        $this->application->registerShared(RemoveImageFromImageListAction::class)
            ->addArgument(ImageListWriteServiceInterface::class)
            ->addArgument(ImageListReadServiceInterface::class)
            ->addArgument(ImageListFactory::class);
    }
}