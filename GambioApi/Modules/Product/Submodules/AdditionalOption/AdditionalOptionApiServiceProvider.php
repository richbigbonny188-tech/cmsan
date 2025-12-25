<?php
/*--------------------------------------------------------------
   AdditionalOptionApiServiceProvider.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\AdditionalOption;

use Gambio\Admin\Modules\Product\Services\AdditionalOptionFilterService;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionReadService;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionWriteService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\CreateAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\DeleteAllAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\DeleteSpecificAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\FetchAllAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\FetchSpecificAdditionalOptionAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\PatchAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\UpdateAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionApiRequestParser;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class AdditionalOptionApiServiceProvider
 *
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption
 */
class AdditionalOptionApiServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            FetchAllAdditionalOptionsAction::class,
            CreateAdditionalOptionsAction::class,
            UpdateAdditionalOptionsAction::class,
            PatchAdditionalOptionsAction::class,
            DeleteAllAdditionalOptionsAction::class,
            FetchSpecificAdditionalOptionAction::class,
            DeleteSpecificAdditionalOptionsAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AdditionalOptionApiRequestParser::class)
            ->addArgument(AdditionalOptionFactory::class);
        $this->application->registerShared(AdditionalOptionApiRequestValidator::class);
        
        $this->application->registerShared(FetchAllAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionApiRequestParser::class)
            ->addArgument(AdditionalOptionFilterService::class);
        
        $this->application->registerShared(CreateAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionApiRequestValidator::class)
            ->addArgument(AdditionalOptionApiRequestParser::class)
            ->addArgument(AdditionalOptionWriteService::class)
            ->addArgument(Url::class);
        
        $this->application->registerShared(UpdateAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionApiRequestValidator::class)
            ->addArgument(AdditionalOptionFactory::class)
            ->addArgument(AdditionalOptionReadService::class)
            ->addArgument(AdditionalOptionWriteService::class);
        
        $this->application->registerShared(PatchAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionReadService::class)
            ->addArgument(AdditionalOptionWriteService::class)
        ->addArgument(AdditionalOptionFactory::class);
            
        $this->application->registerShared(DeleteAllAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionWriteService::class);
        
        $this->application->registerShared(FetchSpecificAdditionalOptionAction::class)
            ->addArgument(AdditionalOptionReadService::class)
            ->addArgument(Url::class);
        
        $this->application->registerShared(DeleteSpecificAdditionalOptionsAction::class)
            ->addArgument(AdditionalOptionWriteService::class)
            ->addArgument(AdditionalOptionReadService::class);
    }
}