<?php
/*--------------------------------------------------------------
   ProductOptionServiceProvider.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Option\Services\OptionRepository;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionsImageListOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionsOptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService;
use Gambio\Admin\Modules\ProductOption\App\Actions\Json\CreateProductOptionsAction;
use Gambio\Admin\Modules\ProductOption\App\Actions\Json\DeleteSpecificProductOptionsAction;
use Gambio\Admin\Modules\ProductOption\App\Actions\Json\FetchAllAvailableOptionsAction;
use Gambio\Admin\Modules\ProductOption\App\Actions\Json\FetchAllProductOptionsAction;
use Gambio\Admin\Modules\ProductOption\App\Actions\Json\FetchSpecificAvailableOptionsAction;
use Gambio\Admin\Modules\ProductOption\App\Actions\Json\FetchSpecificProductOptionAction;
use Gambio\Admin\Modules\ProductOption\App\Actions\Json\UpdateProductOptionsAction;
use Gambio\Admin\Modules\ProductOption\App\Actions\Vue\IndexAction;
use Gambio\Admin\Modules\ProductOption\App\Data\Filter\ProductOptionFilterFactory;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionDeleter;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionInserter;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionMapper;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionReader;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionUpdater;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionFilterService;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionProductVariantOperationPermitter;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionReadService;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionRepository;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionsImageListOperationPermitter;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionsOptionOperationPermitter;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionWriteService;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionFactory;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionFilterService as ProductOptionFilterServiceInterface;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionReadService as ProductOptionReadServiceInterface;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionRepository as ProductOptionRepositoryInterface;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionWriteService as ProductOptionWriteServiceInterface;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsRepository;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionRequestParser;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ProductOptionServiceProvider
 *
 * @package Gambio\Admin\Modules\ProductOption
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *              submodules too. This class will be deleted with 4.11.
 */
class ProductOptionServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ProductOptionFactory::class,
            ProductOptionRepositoryInterface::class,
            ProductOptionFilterServiceInterface::class,
            ProductOptionWriteServiceInterface::class,
            ProductOptionReadServiceInterface::class,
            ProductOptionsOptionOperationPermitter::class,
            ProductOptionsImageListOperationPermitter::class,
            ProductOptionProductVariantOperationPermitter::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ProductOptionFactory::class);
        
        $this->application->registerShared(ProductOptionRepositoryInterface::class, ProductOptionRepository::class)
            ->addArgument(AdditionalOptionRepository::class);
        
        $this->application->registerShared(ProductOptionFilterServiceInterface::class,
                                           ProductOptionFilterService::class)
            ->addArgument(AdditionalOptionFilterService::class);
        
        $this->application->registerShared(ProductOptionWriteServiceInterface::class, ProductOptionWriteService::class)
            ->addArgument(AdditionalOptionWriteService::class);
        
        $this->application->registerShared(ProductOptionReadServiceInterface::class, ProductOptionReadService::class)
            ->addArgument(AdditionalOptionReadService::class);
        
        $this->application->registerShared(ProductOptionsOptionOperationPermitter::class)
            ->addArgument(AdditionalOptionsOptionOperationPermitter::class);
        
        $this->application->registerShared(ProductOptionsImageListOperationPermitter::class)
            ->addArgument(AdditionalOptionsImageListOperationPermitter::class);
        
        $this->application->registerShared(ProductOptionRequestParser::class)->addArgument(ProductOptionFactory::class);
        $this->application->registerShared(ProductOptionRequestValidator::class);
    }
}
