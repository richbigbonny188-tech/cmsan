<?php
/*--------------------------------------------------------------
   AdditionalOptionServiceProvider.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Option\Services\OptionRepository;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\CreateAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\DeleteSpecificAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchAllAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchAllAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchSpecificAdditionalOptionAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchSpecificAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\UpdateAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Vue\IndexAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionDeprecatedEventRaisingRepository;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionFilterService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionProductVariantOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionReadService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionRequestParser;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionRequestValidator;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionsImageListOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionsOptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionWriteService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionDeleter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionInserter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionMapper;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionReader;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionUpdater;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\DeprecatedEventRaisingAdditionalOptionMapper;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterService as AdditionalOptionFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository as AdditionalOptionRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService as AdditionalOptionWriteServiceInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AdditionalOptionServiceProvider
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption
 */
class AdditionalOptionServiceProvider extends AbstractBootableServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AdditionalOptionFactory::class,
            AdditionalOptionRepositoryInterface::class,
            AdditionalOptionFilterServiceInterface::class,
            AdditionalOptionWriteServiceInterface::class,
            AdditionalOptionReadServiceInterface::class,
            AdditionalOptionsOptionOperationPermitter::class,
            AdditionalOptionsImageListOperationPermitter::class,
            AdditionalOptionProductVariantOperationPermitter::class,
            IndexAction::class,
            DeleteSpecificAdditionalOptionsAction::class,
            FetchSpecificAdditionalOptionAction::class,
            UpdateAdditionalOptionsAction::class,
            CreateAdditionalOptionsAction::class,
            FetchAllAdditionalOptionsAction::class,
            FetchAllAvailableOptionsAction::class,
            FetchSpecificAvailableOptionsAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AdditionalOptionFactory::class);
        $this->application->registerShared(AdditionalOptionFilterFactory::class);
        $this->application->registerShared(AdditionalOptionMapper::class)->addArgument(AdditionalOptionFactory::class);
        $this->application->registerShared(DeprecatedEventRaisingAdditionalOptionMapper::class)->addArgument(AdditionalOptionFactory::class);
        $this->application->registerShared(AdditionalOptionReader::class)->addArgument(Connection::class);
        $this->application->registerShared(AdditionalOptionDeleter::class)->addArgument(Connection::class);
        $this->application->registerShared(AdditionalOptionInserter::class)->addArgument(Connection::class);
        $this->application->registerShared(AdditionalOptionUpdater::class)->addArgument(Connection::class);
        
        $this->application->registerShared(AdditionalOptionRepositoryInterface::class, AdditionalOptionDeprecatedEventRaisingRepository::class)
        //$this->application->registerShared(AdditionalOptionRepositoryInterface::class, AdditionalOptionRepository::class)
            ->addArgument(DeprecatedEventRaisingAdditionalOptionMapper::class)
            //->addArgument(AdditionalOptionMapper::class)
            ->addArgument(AdditionalOptionReader::class)
            ->addArgument(AdditionalOptionDeleter::class)
            ->addArgument(AdditionalOptionInserter::class)
            ->addArgument(AdditionalOptionUpdater::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(AdditionalOptionFilterServiceInterface::class,
                                           AdditionalOptionFilterService::class)
            ->addArgument(AdditionalOptionFilterFactory::class)
            ->addArgument(AdditionalOptionRepositoryInterface::class)
            ->addArgument(AdditionalOptionFactory::class);
        
        $this->application->registerShared(AdditionalOptionWriteServiceInterface::class, AdditionalOptionWriteService::class)
            ->addArgument(AdditionalOptionRepositoryInterface::class)
            ->addArgument(AdditionalOptionFactory::class);
        
        $this->application->registerShared(AdditionalOptionReadServiceInterface::class, AdditionalOptionReadService::class)
            ->addArgument(AdditionalOptionRepositoryInterface::class)
            ->addArgument(AdditionalOptionFactory::class);
        
        $this->application->registerShared(AdditionalOptionsOptionOperationPermitter::class)
            ->addArgument(AdditionalOptionReader::class);
        
        $this->application->registerShared(AdditionalOptionsImageListOperationPermitter::class)
            ->addArgument(AdditionalOptionReader::class);
        
        $this->application->registerShared(IndexAction::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(AdminMenuService::class)
            ->addArgument(LanguageService::class)
            ->addArgument(ConfigurationFinder::class);
        
        $this->application->registerShared(DeleteSpecificAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionWriteServiceInterface::class)
            ->addArgument(AdditionalOptionReadServiceInterface::class);
        
        $this->application->registerShared(FetchSpecificAdditionalOptionAction::class)
            ->addArgument(AdditionalOptionReadServiceInterface::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(OptionFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(ProductPriceConversionService::class);
        
        $this->application->registerShared(UpdateAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionFactory::class)
            ->addArgument(AdditionalOptionReadServiceInterface::class)
            ->addArgument(AdditionalOptionWriteServiceInterface::class)
            ->addArgument(ProductPriceConversionService::class)
            ->addArgument(AdditionalOptionRequestParser::class);
        
        $this->application->registerShared(AdditionalOptionRequestParser::class)->addArgument(AdditionalOptionFactory::class);
        $this->application->registerShared(AdditionalOptionRequestValidator::class);
        
        $this->application->registerShared(CreateAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionRequestValidator::class)
            ->addArgument(AdditionalOptionRequestParser::class)
            ->addArgument(AdditionalOptionWriteServiceInterface::class)
            ->addArgument(AdditionalOptionReadServiceInterface::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(ProductPriceConversionService::class);
        
        $this->application->registerShared(FetchAllAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionReadServiceInterface::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(OptionFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(ProductPriceConversionService::class);
        
        $this->application->registerShared(FetchAllAvailableOptionsAction::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(AdditionalOptionReadServiceInterface::class)
            ->addArgument(ProductVariantsReadServiceInterface::class);
        
        $this->application->registerShared(FetchSpecificAvailableOptionsAction::class)
            ->addArgument(OptionReadServiceInterface::class)
            ->addArgument(AdditionalOptionReadServiceInterface::class)
            ->addArgument(ProductVariantsReadServiceInterface::class);
        
        $this->application->registerShared(AdditionalOptionProductVariantOperationPermitter::class)
            ->addArgument(AdditionalOptionRepositoryInterface::class)
            ->addArgument(AdditionalOptionFactory::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(OptionRepository::class)
            ->invokeMethod('registerOperationPermitter', [AdditionalOptionsOptionOperationPermitter::class]);
        
        $this->application->inflect(ImageListRepository::class)
            ->invokeMethod('registerOperationPermitter', [AdditionalOptionsImageListOperationPermitter::class]);
        
        $this->application->inflect(ProductVariantsRepository::class)
            ->invokeMethod('registerOperationPermitter', [AdditionalOptionProductVariantOperationPermitter::class]);
    }
}
