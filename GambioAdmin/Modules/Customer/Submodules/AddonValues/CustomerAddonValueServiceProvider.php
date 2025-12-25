<?php
/*--------------------------------------------------------------
   CustomerAddonValueServiceProvider.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueFilterService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueReadService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueRepository;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueStorage;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueWriteService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\CustomerAddonValueMapper;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\CustomerAddonValueReader;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\CustomerAddonValueWriter;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\EventListeners\DeleteAddonValuesAfterDeletionOfACustomerEventListener;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFactory;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFilterFactory;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFilterService as CustomerAddonValueFilterServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueReadService as CustomerAddonValueReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueRepository as CustomerAddonValueRepositoryInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueStorage as CustomerAddonValueStorageInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService as CustomerAddonValueWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class CustomerAddonValueServiceProvider
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue
 * @codeCoverageIgnore
 */
class CustomerAddonValueServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerAddonValueFactory::class,
            CustomerAddonValueFactory::class,
            CustomerAddonValueRepositoryInterface::class,
            CustomerAddonValueWriteServiceInterface::class,
            CustomerAddonValueReadServiceInterface::class,
            CustomerAddonValueFilterServiceInterface::class,
            CustomerAddonValueStorageInterface::class,
            DeleteAddonValuesAfterDeletionOfACustomerEventListener::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerAddonValueFactory::class);
        
        $this->application->registerShared(CustomerAddonValueMapper::class)
            ->addArgument(CustomerAddonValueFactory::class);
        $this->application->registerShared(CustomerAddonValueReader::class)->addArgument(Connection::class);
        $this->application->registerShared(CustomerAddonValueWriter::class)->addArgument(Connection::class);
        
        $this->application->registerShared(CustomerAddonValueRepositoryInterface::class,
                                           CustomerAddonValueRepository::class)
            ->addArgument(CustomerAddonValueReader::class)
            ->addArgument(CustomerAddonValueWriter::class)
            ->addArgument(CustomerAddonValueMapper::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(CustomerAddonValueFilterFactory::class);
        
        $this->application->registerShared(CustomerAddonValueFilterServiceInterface::class,
                                           CustomerAddonValueFilterService::class)
            ->addArgument(CustomerAddonValueRepositoryInterface::class)
            ->addArgument(CustomerAddonValueFactory::class)
            ->addArgument(CustomerAddonValueFilterFactory::class);
        
        $this->application->registerShared(CustomerAddonValueReadServiceInterface::class,
                                           CustomerAddonValueReadService::class)
            ->addArgument(CustomerAddonValueRepositoryInterface::class)
            ->addArgument(CustomerAddonValueFactory::class);
        
        $this->application->registerShared(CustomerAddonValueWriteServiceInterface::class,
                                           CustomerAddonValueWriteService::class)
            ->addArgument(CustomerAddonValueRepositoryInterface::class)
            ->addArgument(CustomerAddonValueFactory::class);
        
        $this->application->registerShared(DeleteAddonValuesAfterDeletionOfACustomerEventListener::class)
            ->addArgument(CustomerAddonValueWriteServiceInterface::class);
        
        $this->application->registerShared(CustomerAddonValueStorageInterface::class, CustomerAddonValueStorage::class)
            ->addArgument(CustomerAddonValueRepositoryInterface::class)
            ->addArgument(CustomerAddonValueFactory::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->attachEventListener(CustomerDeleted::class,
                                                DeleteAddonValuesAfterDeletionOfACustomerEventListener::class);
    }
}