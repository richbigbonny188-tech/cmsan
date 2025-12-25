<?php
/*--------------------------------------------------------------
   CustomerApiServiceProvider.php 2022-07-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Customer;

use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerFilterService;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService;
use Gambio\Api\Modules\Customer\App\Actions\CreateCustomerAction;
use Gambio\Api\Modules\Customer\App\Actions\DeleteMultipleCustomersAction;
use Gambio\Api\Modules\Customer\App\Actions\DeleteOutdatedGuestAccountsAction;
use Gambio\Api\Modules\Customer\App\Actions\FetchAllCustomersAction;
use Gambio\Api\Modules\Customer\App\Actions\FetchSpecificCustomerAction;
use Gambio\Api\Modules\Customer\App\Actions\UpdateCustomerAction;
use Gambio\Api\Modules\Customer\App\Actions\UpdateCustomerFavoriteStateAction;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestParser;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class CustomerApiServiceProvider
 *
 * @package Gambio\Api\Modules\CustomerMemo
 * @codeCoverageIgnore
 */
class CustomerApiServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            FetchAllCustomersAction::class,
            FetchSpecificCustomerAction::class,
            DeleteMultipleCustomersAction::class,
            CreateCustomerAction::class,
            UpdateCustomerAction::class,
            UpdateCustomerFavoriteStateAction::class,
            DeleteOutdatedGuestAccountsAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerApiRequestParser::class)
            ->addArgument(CustomerFactory::class)
            ->addArgument(CustomerAddressFactory::class);
        $this->application->registerShared(CustomerApiRequestValidator::class);
        
        $this->application->registerShared(FetchAllCustomersAction::class)
            ->addArgument(CustomerApiRequestParser::class)
            ->addArgument(CustomerFilterService::class);
        
        $this->application->registerShared(FetchSpecificCustomerAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(CustomerMemoReadService::class)
            ->addArgument(CustomerApiRequestParser::class);
        
        $this->application->registerShared(DeleteMultipleCustomersAction::class)
            ->addArgument(CustomerWriteService::class);
        
        $this->application->registerShared(DeleteOutdatedGuestAccountsAction::class)
            ->addArgument(CustomerWriteService::class);
        
        $this->application->registerShared(CreateCustomerAction::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerApiRequestValidator::class)
            ->addArgument(CustomerApiRequestParser::class);
        
        $this->application->registerShared(UpdateCustomerAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerApiRequestValidator::class)
            ->addArgument(CustomerFactory::class);
        
        $this->application->registerShared(UpdateCustomerFavoriteStateAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerApiRequestValidator::class);
    }
}