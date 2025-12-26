<?php
/* --------------------------------------------------------------
   OptionApiServiceProvider.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option;

use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionFilterService;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Api\Modules\Option\App\Actions\AddANewImageToAnOptionValueAction;
use Gambio\Api\Modules\Option\App\Actions\CreateOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\CreateOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\DeleteOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\DeleteOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\FetchAllOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\FetchAllOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\FetchSpecificOptionAction;
use Gambio\Api\Modules\Option\App\Actions\FetchSpecificOptionValueAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionsSortingOrderAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionValuesSortingOrderAction;
use Gambio\Api\Modules\Option\App\OptionApiRequestParser;
use Gambio\Api\Modules\Option\App\OptionApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class OptionApiServiceProvider
 *
 * @package Gambio\Api\Modules\Option
 * @codeCoverageIgnore
 */
class OptionApiServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CreateOptionsAction::class,
            DeleteOptionsAction::class,
            FetchSpecificOptionAction::class,
            FetchAllOptionsAction::class,
            UpdateOptionsAction::class,
            UpdateOptionsSortingOrderAction::class,
            FetchAllOptionValuesAction::class,
            FetchSpecificOptionValueAction::class,
            CreateOptionValuesAction::class,
            UpdateOptionValuesAction::class,
            UpdateOptionValuesSortingOrderAction::class,
            DeleteOptionValuesAction::class,
            AddANewImageToAnOptionValueAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OptionApiRequestParser::class)->addArgument(OptionFactory::class);
        
        $this->application->registerShared(OptionApiRequestValidator::class)->addArgument(LanguageService::class);
        
        $this->application->registerShared(CreateOptionsAction::class)
            ->addArgument(OptionApiRequestParser::class)
            ->addArgument(OptionApiRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(Url::class);
        
        $this->application->registerShared(DeleteOptionsAction::class)->addArgument(OptionWriteService::class);
        
        $this->application->registerShared(FetchSpecificOptionAction::class)->addArgument(OptionReadService::class);
        
        $this->application->registerShared(FetchAllOptionsAction::class)
            ->addArgument(OptionApiRequestParser::class)
            ->addArgument(OptionFilterService::class);
        
        $this->application->registerShared(UpdateOptionsAction::class)
            ->addArgument(OptionApiRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(UpdateOptionsSortingOrderAction::class)
            ->addArgument(OptionApiRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(FetchAllOptionValuesAction::class)
            ->addArgument(OptionApiRequestParser::class)
            ->addArgument(OptionReadService::class);
        
        $this->application->registerShared(FetchSpecificOptionValueAction::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(Url::class);
        
        $this->application->registerShared(CreateOptionValuesAction::class)
            ->addArgument(OptionApiRequestParser::class)
            ->addArgument(OptionApiRequestValidator::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(Url::class);
        
        $this->application->registerShared(UpdateOptionValuesAction::class)
            ->addArgument(OptionApiRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(UpdateOptionValuesSortingOrderAction::class)
            ->addArgument(OptionApiRequestValidator::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(DeleteOptionValuesAction::class)
            ->addArgument(OptionWriteService::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionFactory::class);
        
        $this->application->registerShared(AddANewImageToAnOptionValueAction::class)
            ->addArgument(OptionReadService::class)
            ->addArgument(OptionWriteService::class);
    }
}