<?php
/* --------------------------------------------------------------
   OptionServiceProvider.php 2023-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Admin\Modules\Option\App\Actions\Json\CreateOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\CreateOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\DeleteOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\DeleteOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\FetchAllOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\FetchAllOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\FetchSpecificOptionAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionsSortingOrderAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionValuesSortingOrderAction;
use Gambio\Admin\Modules\Option\App\Actions\Vue\IndexAction;
use Gambio\Admin\Modules\Option\App\Data\AdminOptionResponseSorter;
use Gambio\Admin\Modules\Option\App\Data\Filter\OptionFilterFactory;
use Gambio\Admin\Modules\Option\App\Data\LegacyWriterForOptions;
use Gambio\Admin\Modules\Option\App\Data\OptionMapper;
use Gambio\Admin\Modules\Option\App\Data\OptionReader;
use Gambio\Admin\Modules\Option\App\Data\ProductPriceConversionReader;
use Gambio\Admin\Modules\Option\App\Data\OptionWriter;
use Gambio\Admin\Modules\Option\App\OptionRequestParser;
use Gambio\Admin\Modules\Option\App\OptionRequestValidator;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionFilterService;
use Gambio\Admin\Modules\Option\Services\OptionOperationPermitter;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionRepository;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\TextManager\Services\TextManager;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class OptionServiceProvider
 *
 * @package Gambio\Admin\Modules\Option2
 */
class OptionServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            OptionFactory::class,
            OptionFilterService::class,
            OptionOperationPermitter::class,
            OptionReadService::class,
            OptionRepository::class,
            OptionWriteService::class,
            ProductPriceConversionService::class,
            IndexAction::class,
            FetchSpecificOptionAction::class,
            DeleteOptionsAction::class,
            FetchAllOptionsAction::class,
            FetchAllOptionValuesAction::class,
            DeleteOptionValuesAction::class,
            UpdateOptionValuesSortingOrderAction::class,
            CreateOptionValuesAction::class,
            UpdateOptionValuesAction::class,
            UpdateOptionsAction::class,
            CreateOptionsAction::class,
            UpdateOptionsSortingOrderAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AdminOptionResponseSorter::class);
        $this->application->registerShared(OptionRequestParser::class)->addArgument(OptionFactory::class);
        
        $this->application->registerShared(OptionRequestValidator::class)->addArgument(LanguageService::class);
        
        $this->application->registerShared(OptionFactory::class);
        
        $this->application->registerShared(OptionFilterFactory::class);
        
        $this->application->registerShared(LegacyWriterForOptions::class)
            ->addArgument(Connection::class)
            ->addArgument(LanguageService::class);
        
        $this->application->registerShared(OptionMapper::class)->addArgument(OptionFactory::class);
        
        $this->application->registerShared(OptionReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(OptionWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(LanguageService::class)
            ->addArgument(LegacyWriterForOptions::class);
        
        $this->application->registerShared(OptionRepository::class, App\OptionRepository::class)
            ->addArgument(OptionMapper::class)
            ->addArgument(OptionReader::class)
            ->addArgument(OptionWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(OptionFilterService::class, App\OptionFilterService::class)
            ->addArgument(OptionRepository::class)
            ->addArgument(OptionFilterFactory::class);
        
        $this->application->registerShared(OptionReadService::class, App\OptionReadService::class)
            ->addArgument(OptionRepository::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(OptionWriteService::class, App\OptionWriteService::class)
            ->addArgument(OptionRepository::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(IndexAction::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(LanguageService::class)
            ->addArgument(TextManager::class)
            ->addArgument(ConfigurationFinder::class);
        
        $this->application->registerShared(FetchSpecificOptionAction::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(AdminOptionResponseSorter::class)
            ->addArgument(LanguageReadService::class);
        
        $this->application->registerShared(FetchAllOptionsAction::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(AdminOptionResponseSorter::class)
            ->addArgument(LanguageReadService::class);
        
        $this->application->registerShared(DeleteOptionsAction::class)->addArgument(OptionWriteService::class);
        
        $this->application->registerShared(FetchAllOptionValuesAction::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(AdminOptionResponseSorter::class);
        
        $this->application->registerShared(DeleteOptionValuesAction::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(UpdateOptionValuesSortingOrderAction::class)
            ->addArgument(OptionRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(CreateOptionValuesAction::class)
            ->addArgument(OptionRequestParser::class)
            ->addArgument(OptionRequestValidator::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionWriteService::class);
        
        $this->application->registerShared(UpdateOptionValuesAction::class)
            ->addArgument(OptionRequestParser::class)
            ->addArgument(OptionRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(UpdateOptionsAction::class)
            ->addArgument(OptionRequestParser::class)
            ->addArgument(OptionRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(CreateOptionsAction::class)
            ->addArgument(OptionRequestParser::class)
            ->addArgument(OptionRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class);
        
        $this->application->registerShared(UpdateOptionsSortingOrderAction::class)
            ->addArgument(OptionRequestValidator::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionWriteService::class);
    }
}
