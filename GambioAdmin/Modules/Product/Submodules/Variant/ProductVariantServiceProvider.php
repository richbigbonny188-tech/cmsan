<?php
/*--------------------------------------------------------------
   ProductVariantServiceProvider.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Product\Submodules\Variant;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository;
use Gambio\Admin\Modules\Option\Model\Events\OptionDetailsUpdated;
use Gambio\Admin\Modules\Option\Model\Events\OptionValueUpdated;
use Gambio\Admin\Modules\Option\Services\OptionRepository;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadRepository;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\Filter\ProductVariantFilterFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsDeleter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsInserter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsMapper;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners\RebuildProductsPropertiesIndexOnUpdatedOptionDetail;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners\RebuildProductsPropertiesIndexOnUpdatedOptionValue;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\EventListeners\RecalculateProductVariantsPricesOnOptionValueUpdatedEventListener;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantCombinationGenerator;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantDeprecatedEventRaisingRepository;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantProductDownloadOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantProductOptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsFilterService;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsGenerationService;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsImageListOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsOptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsReadService;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsRepository;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsWriteService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\DeprecatedEventRaisingProductVariantFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsFilterService as ProductVariantsFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsGenerationService as ProductVariantsGenerationServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository as ProductVariantsRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsWriteService as ProductVariantsWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ProductVariantServiceProvider
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant
 * @codeCoverageIgnore
 */
class ProductVariantServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ProductVariantsFilterServiceInterface::class,
            ProductVariantsReadServiceInterface::class,
            ProductVariantsWriteServiceInterface::class,
            ProductVariantsRepositoryInterface::class,
            ProductVariantsGenerationServiceInterface::class,
            ProductVariantsOptionOperationPermitter::class,
            ProductVariantProductDownloadOperationPermitter::class,
            ProductVariantProductOptionOperationPermitter::class,
            ProductVariantsImageListOperationPermitter::class,
            ProductVariantFactory::class,
            RebuildProductsPropertiesIndexOnUpdatedOptionDetail::class,
            RebuildProductsPropertiesIndexOnUpdatedOptionValue::class,
            Services\RecalculateProductVariantPriceService::class,
            RecalculateProductVariantsPricesOnOptionValueUpdatedEventListener::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ProductVariantFilterFactory::class);
        $this->application->registerShared(ProductVariantFactory::class);
        $this->application->registerShared(DeprecatedEventRaisingProductVariantFactory::class);
        $this->application->registerShared(ProductVariantsReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductVariantsDeleter::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductVariantsInserter::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductVariantsUpdater::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductVariantsMapper::class)->addArgument(DeprecatedEventRaisingProductVariantFactory::class);
        $this->application->registerShared(ProductVariantCombinationGenerator::class)
            ->addArgument(ProductVariantFactory::class);
        
        $this->application->registerShared(ProductVariantsRepositoryInterface::class, ProductVariantDeprecatedEventRaisingRepository::class)
            ->addArgument(ProductVariantsReader::class)
            ->addArgument(ProductVariantsDeleter::class)
            ->addArgument(ProductVariantsInserter::class)
            ->addArgument(ProductVariantsUpdater::class)
            ->addArgument(ProductVariantsMapper::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(ProductVariantsFilterServiceInterface::class,
                                           ProductVariantsFilterService::class)
            ->addArgument(ProductVariantFilterFactory::class)
            ->addArgument(ProductVariantsRepositoryInterface::class)
            ->addArgument(ProductVariantFactory::class);
        
        $this->application->registerShared(ProductVariantsReadServiceInterface::class,
                                           ProductVariantsReadService::class)
            ->addArgument(ProductVariantsRepositoryInterface::class)
            ->addArgument(ProductVariantFactory::class);
        
        $this->application->registerShared(ProductVariantsWriteServiceInterface::class,
                                           ProductVariantsWriteService::class)
            ->addArgument(ProductVariantsRepositoryInterface::class)
            ->addArgument(ProductVariantFactory::class);
        
        $this->application->registerShared(ProductVariantsGenerationServiceInterface::class,
                                           ProductVariantsGenerationService::class)
            ->addArgument(ProductVariantsRepositoryInterface::class)
            ->addArgument(ProductVariantFactory::class)
            ->addArgument(ProductVariantCombinationGenerator::class)
            ->addArgument(ConfigurationService::class);
        
        $this->application->registerShared(ProductVariantsOptionOperationPermitter::class)
            ->addArgument(ProductVariantsReader::class);
        
        $this->application->registerShared(ProductVariantsImageListOperationPermitter::class)
            ->addArgument(ProductVariantsReader::class);
        
        $this->application->registerShared(RebuildProductsPropertiesIndexOnUpdatedOptionDetail::class)
            ->addArgument(ProductVariantsReader::class)
            ->addArgument(ProductVariantsUpdater::class);
        
        $this->application->registerShared(RebuildProductsPropertiesIndexOnUpdatedOptionValue::class)
            ->addArgument(ProductVariantsReader::class)
            ->addArgument(ProductVariantsUpdater::class);
        
        $this->application->registerShared(ProductVariantProductDownloadOperationPermitter::class)
            ->addArgument(ProductVariantsReader::class);
        
        $this->application->registerShared(ProductVariantProductOptionOperationPermitter::class)
            ->addArgument(ProductVariantsReader::class);
        
        $this->application->registerShared(RecalculateProductVariantsPricesOnOptionValueUpdatedEventListener::class)
            ->addArgument(Services\RecalculateProductVariantPriceService::class);
        
        $this->application->registerShared(Services\RecalculateProductVariantPriceService::class,
                                           App\RecalculateProductVariantPriceService::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(ProductPriceConversionService::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(OptionRepository::class)
            ->invokeMethod('registerOperationPermitter', [ProductVariantsOptionOperationPermitter::class]);
        
        $this->application->inflect(ImageListRepository::class)
            ->invokeMethod('registerOperationPermitter', [ProductVariantsImageListOperationPermitter::class]);
        
        $this->application->attachEventListener(OptionDetailsUpdated::class,
                                                RebuildProductsPropertiesIndexOnUpdatedOptionDetail::class);
        
        $this->application->attachEventListener(OptionValueUpdated::class,
                                                RebuildProductsPropertiesIndexOnUpdatedOptionValue::class);
        
        $this->application->inflect(ProductDownloadRepository::class)
            ->invokeMethod('registerOperationPermitter', [ProductVariantProductDownloadOperationPermitter::class]);
        
        $this->application->inflect(AdditionalOptionRepository::class)
            ->invokeMethod('registerOperationPermitter', [ProductVariantProductOptionOperationPermitter::class]);
        
        $this->application->attachEventListener(OptionValueUpdated::class,
                                                RecalculateProductVariantsPricesOnOptionValueUpdatedEventListener::class);
    }
}