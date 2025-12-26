<?php
/*--------------------------------------------------------------------------------------------------
    ProductDownloadServiceProvider.php 2023-06-26
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\CreateProductDownloadsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\DeleteSpecificProductDownloadAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchAllAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchAllProductDownloadsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchSpecificAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchSpecificProductDownloadAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\UpdateProductDownloadsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Vue\IndexAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\DeprecatedEventRaisingProductDownloadMapper;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\Filter\ProductDownloadFilterFactory;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadDeleter;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadInserter;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadMapper;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadReader;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadUpdater;
use Gambio\Admin\Modules\Product\Submodules\Download\App\DeprecatedEventRaisingProductDownloadRepository;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadFilterService;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadReadService;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadRepository;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadsImageListOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadsOptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadWriteService;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFactory;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFilterService as ProductDownloadFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository as ProductDownloadRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadWriteService as ProductDownloadWriteServiceInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadRequestParser;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 *
 */
class ProductDownloadServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            IndexAction::class,
            DeleteSpecificProductDownloadAction::class,
            FetchSpecificProductDownloadAction::class,
            UpdateProductDownloadsAction::class,
            CreateProductDownloadsAction::class,
            FetchAllProductDownloadsAction::class,
            FetchAllAvailableOptionsAction::class,
            FetchSpecificAvailableOptionsAction::class,
            ProductDownloadFilterServiceInterface::class,
            ProductDownloadWriteServiceInterface::class,
            ProductDownloadReadServiceInterface::class,
        ];
    }
    
    
    /**
     * @inheritcDoc
     */
    public function register(): void
    {
        $this->application->registerShared(IndexAction::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(AdminMenuService::class)
            ->addArgument(LanguageService::class)
            ->addArgument(ConfigurationFinder::class);
        
        $this->application->registerShared(ProductDownloadFactory::class);
        $this->application->registerShared(ProductDownloadFilterFactory::class);
        $this->application->registerShared(ProductDownloadMapper::class)->addArgument(ProductDownloadFactory::class);
        $this->application->registerShared(DeprecatedEventRaisingProductDownloadMapper::class)->addArgument(ProductDownloadFactory::class);
        $this->application->registerShared(ProductDownloadReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductDownloadDeleter::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductDownloadInserter::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductDownloadUpdater::class)->addArgument(Connection::class);
        
        $this->application->registerShared(ProductDownloadRepositoryInterface::class, DeprecatedEventRaisingProductDownloadRepository::class)
            ->addArgument(ProductDownloadMapper::class)
            ->addArgument(ProductDownloadReader::class)
            ->addArgument(ProductDownloadDeleter::class)
            ->addArgument(ProductDownloadInserter::class)
            ->addArgument(ProductDownloadUpdater::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(ProductDownloadFilterServiceInterface::class,
                                           ProductDownloadFilterService::class)
            ->addArgument(ProductDownloadFilterFactory::class)
            ->addArgument(ProductDownloadRepositoryInterface::class)
            ->addArgument(ProductDownloadFactory::class);
        
        $this->application->registerShared(ProductDownloadWriteServiceInterface::class,
                                           ProductDownloadWriteService::class)
            ->addArgument(ProductDownloadRepositoryInterface::class)
            ->addArgument(ProductDownloadFactory::class);
        
        $this->application->registerShared(ProductDownloadReadServiceInterface::class,
                                           ProductDownloadReadService::class)
            ->addArgument(ProductDownloadRepositoryInterface::class)
            ->addArgument(ProductDownloadFactory::class);
        
        $this->application->registerShared(ProductDownloadsOptionOperationPermitter::class)
            ->addArgument(ProductDownloadReader::class);
        
        $this->application->registerShared(ProductDownloadsImageListOperationPermitter::class)
            ->addArgument(ProductDownloadReader::class);
        
        $this->application->registerShared(DeleteSpecificProductDownloadAction::class)
            ->addArgument(ProductDownloadWriteServiceInterface::class)
            ->addArgument(ProductDownloadReadServiceInterface::class);
        
        $this->application->registerShared(FetchSpecificProductDownloadAction::class)
            ->addArgument(ProductDownloadReadServiceInterface::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(OptionFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(ProductPriceConversionService::class);
        
        $this->application->registerShared(UpdateProductDownloadsAction::class)
            ->addArgument(ProductDownloadFactory::class)
            ->addArgument(ProductDownloadReadServiceInterface::class)
            ->addArgument(ProductDownloadWriteServiceInterface::class)
            ->addArgument(Connection::class)
            ->addArgument(ProductPriceConversionService::class)
            ->addArgument(ProductDownloadRequestParser::class);
        
        $this->application->registerShared(ProductDownloadRequestParser::class)
            ->addArgument(ProductDownloadFactory::class);
        $this->application->registerShared(ProductDownloadRequestValidator::class);
        
        $this->application->registerShared(CreateProductDownloadsAction::class)
            ->addArgument(ProductDownloadRequestValidator::class)
            ->addArgument(ProductDownloadRequestParser::class)
            ->addArgument(ProductDownloadWriteServiceInterface::class)
            ->addArgument(ProductDownloadReadServiceInterface::class)
            ->addArgument(Connection::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(ProductPriceConversionService::class);
        
        $this->application->registerShared(FetchAllProductDownloadsAction::class)
            ->addArgument(ProductDownloadReadServiceInterface::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(OptionFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(ProductPriceConversionService::class);
        
        $this->application->registerShared(FetchAllAvailableOptionsAction::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(ProductDownloadReadServiceInterface::class)
            ->addArgument(ProductVariantsReadServiceInterface::class);
        
        $this->application->registerShared(FetchSpecificAvailableOptionsAction::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(ProductDownloadReadServiceInterface::class)
            ->addArgument(ProductVariantsReadServiceInterface::class);
    }
}