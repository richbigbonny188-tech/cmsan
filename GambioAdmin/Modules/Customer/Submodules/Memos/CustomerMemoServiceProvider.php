<?php
/*--------------------------------------------------------------
   CustomerMemoServiceProvider.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Actions\Json\CreateCustomerMemoAction;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Actions\Json\DeleteCustomerMemoAction;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\CustomerMemoFilterService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\CustomerMemoReadService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\CustomerMemoRepository;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\CustomerMemoWriteService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\CustomerMemoMapper;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\CustomerMemoReader;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\CustomerMemoWriter;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\EventListeners\DeleteCustomerMemosOnCustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFilterFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFilterService as CustomerMemoFilterServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService as CustomerMemoReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoRepository as CustomerMemoRepositoryInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService as CustomerMemoWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class CustomerMemoServiceProvider
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos
 * @codeCoverageIgnore
 */
class CustomerMemoServiceProvider extends AbstractBootableServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerMemoFactory::class,
            CustomerMemoRepositoryInterface::class,
            CustomerMemoWriteServiceInterface::class,
            CustomerMemoReadServiceInterface::class,
            CustomerMemoFilterServiceInterface::class,
            DeleteCustomerMemosOnCustomerDeleted::class,
            DeleteCustomerMemoAction::class,
            CreateCustomerMemoAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerMemoFactory::class);
        
        $this->application->registerShared(CustomerMemoMapper::class);
        $this->application->registerShared(CustomerMemoReader::class)->addArgument(Connection::class);
        $this->application->registerShared(CustomerMemoWriter::class)->addArgument(Connection::class);
        
        $this->application->registerShared(CustomerMemoRepositoryInterface::class, CustomerMemoRepository::class)
            ->addArgument(CustomerMemoReader::class)
            ->addArgument(CustomerMemoWriter::class)
            ->addArgument(CustomerMemoMapper::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(CustomerMemoFilterFactory::class);
        
        $this->application->registerShared(CustomerMemoFilterServiceInterface::class, CustomerMemoFilterService::class)
            ->addArgument(CustomerMemoRepositoryInterface::class)
            ->addArgument(CustomerMemoFactory::class)
            ->addArgument(CustomerMemoFilterFactory::class);
        
        $this->application->registerShared(CustomerMemoReadServiceInterface::class, CustomerMemoReadService::class)
            ->addArgument(CustomerMemoRepositoryInterface::class)
            ->addArgument(CustomerMemoFactory::class);
        
        $this->application->registerShared(CustomerMemoWriteServiceInterface::class, CustomerMemoWriteService::class)
            ->addArgument(CustomerMemoRepositoryInterface::class)
            ->addArgument(CustomerMemoFactory::class);
        
        $this->application->registerShared(DeleteCustomerMemosOnCustomerDeleted::class)
            ->addArgument(CustomerMemoRepositoryInterface::class)
            ->addArgument(CustomerMemoFactory::class);
        
        $this->application->registerShared(DeleteCustomerMemoAction::class)
            ->addArgument(CustomerMemoWriteServiceInterface::class);
        
        $this->application->registerShared(CreateCustomerMemoAction::class)
            ->addArgument(CustomerMemoWriteServiceInterface::class)
            ->addArgument(UserPreferences::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->attachEventListener(CustomerDeleted::class, DeleteCustomerMemosOnCustomerDeleted::class);
    }
}