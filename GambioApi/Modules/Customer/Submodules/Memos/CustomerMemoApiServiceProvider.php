<?php
/*--------------------------------------------------------------
   CustomerMemoApiServiceProvider.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Customer\Submodules\Memos;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFilterService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService;
use Gambio\Api\Application\Auth\Interfaces\WebRequestUserIdentificationService;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\CreateCustomerMemoAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\DeleteAllCustomerMemoAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\DeleteMultipleCustomerMemosAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\FetchAllCustomerMemosAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\FetchSpecificCustomerMemoAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\UpdateCustomerMemoAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\CustomerMemoApiRequestParser;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\CustomerMemoApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class CustomerMemoApiServiceProvider
 *
 * @package Gambio\Api\Modules\Customer\Submodules\Memos
 * @codeCoverageIgnore
 */
class CustomerMemoApiServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerMemoApiRequestValidator::class,
            CustomerMemoApiRequestParser::class,
            FetchAllCustomerMemosAction::class,
            FetchSpecificCustomerMemoAction::class,
            DeleteAllCustomerMemoAction::class,
            DeleteMultipleCustomerMemosAction::class,
            CreateCustomerMemoAction::class,
            UpdateCustomerMemoAction::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerMemoApiRequestValidator::class);
        $this->application->registerShared(CustomerMemoApiRequestParser::class)
            ->addArgument(WebRequestUserIdentificationService::class)
            ->addArgument(CustomerMemoFactory::class);
        
        $this->application->registerShared(FetchAllCustomerMemosAction::class)
            ->addArgument(CustomerMemoApiRequestParser::class)
            ->addArgument(CustomerMemoFilterService::class);
        
        $this->application->registerShared(FetchSpecificCustomerMemoAction::class)
            ->addArgument(CustomerMemoReadService::class)
            ->addArgument(CustomerMemoApiRequestParser::class);
        
        $this->application->registerShared(DeleteAllCustomerMemoAction::class)
            ->addArgument(CustomerMemoWriteService::class);
        
        $this->application->registerShared(DeleteMultipleCustomerMemosAction::class)
            ->addArgument(CustomerMemoWriteService::class)
            ->addArgument(CustomerMemoReadService::class);
    
        $this->application->registerShared(CreateCustomerMemoAction::class)
            ->addArgument(CustomerMemoWriteService::class)
            ->addArgument(CustomerMemoApiRequestValidator::class)
            ->addArgument(CustomerMemoApiRequestParser::class);
    
        $this->application->registerShared(UpdateCustomerMemoAction::class)
            ->addArgument(CustomerMemoReadService::class)
            ->addArgument(CustomerMemoWriteService::class)
            ->addArgument(CustomerMemoApiRequestValidator::class);
            
    }
}