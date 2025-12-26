<?php
/*--------------------------------------------------------------
   CustomerAddonValueApiServiceProvider.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFilterService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueReadService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\CreateCustomerAddonValueAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\DeleteAllCustomerAddonValuesAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\DeleteMultipleCustomerAddonValuesAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\FetchAllCustomerAddonValuesAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\FetchSpecificCustomerAddonValueAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\UpdateCustomerAddonValueAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestParser;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class CustomerAddonValueApiServiceProvider
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues
 * @codeCoverageIgnore
 */
class CustomerAddonValueApiServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CreateCustomerAddonValueAction::class,
            DeleteAllCustomerAddonValuesAction::class,
            DeleteMultipleCustomerAddonValuesAction::class,
            FetchAllCustomerAddonValuesAction::class,
            FetchSpecificCustomerAddonValueAction::class,
            UpdateCustomerAddonValueAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerAddonValueApiRequestValidator::class);
        $this->application->registerShared(CustomerAddonValueApiRequestParser::class);
    
        $this->application->registerShared(CreateCustomerAddonValueAction::class)
            ->addArgument(CustomerAddonValueWriteService::class)
            ->addArgument(CustomerAddonValueApiRequestValidator::class)
            ->addArgument(CustomerAddonValueApiRequestParser::class);
    
        $this->application->registerShared(DeleteAllCustomerAddonValuesAction::class)
            ->addArgument(CustomerAddonValueWriteService::class);
    
        $this->application->registerShared(DeleteMultipleCustomerAddonValuesAction::class)
            ->addArgument(CustomerAddonValueWriteService::class)
            ->addArgument(CustomerAddonValueApiRequestParser::class);
    
        $this->application->registerShared(FetchAllCustomerAddonValuesAction::class)
            ->addArgument(CustomerAddonValueApiRequestParser::class)
            ->addArgument(CustomerAddonValueFilterService::class);
    
        $this->application->registerShared(FetchSpecificCustomerAddonValueAction::class)
            ->addArgument(CustomerAddonValueReadService::class)
            ->addArgument(CustomerAddonValueApiRequestParser::class);
    
        $this->application->registerShared(UpdateCustomerAddonValueAction::class)
            ->addArgument(CustomerAddonValueReadService::class)
            ->addArgument(CustomerAddonValueWriteService::class)
            ->addArgument(CustomerAddonValueApiRequestValidator::class);
    }
}