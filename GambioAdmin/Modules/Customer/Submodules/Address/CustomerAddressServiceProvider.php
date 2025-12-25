<?php
/*--------------------------------------------------------------
   CustomerAddressServiceProvider.php 2022-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Actions\Json\FetchCustomerDefaultAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Actions\Json\UpdateCustomersDefaultAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\CustomerAddressRepository;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\CustomerDefaultAddressReadService;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\CustomerDefaultAddressRepository;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\CustomerDefaultAddressWriteService;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressMapper;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressReader;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressWriter;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressCountryTranslationRepository;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\EventListeners\DeleteAddressesAfterDeletionOfACustomerEventListener;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressCountryTranslationRepository as CustomerCountryTranslationRepositoryInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressRepository as CustomerAddressRepositoryInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressReadService as CustomerDefaultAddressReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressRepository as CustomerDefaultAddressRepositoryInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressWriteService as CustomerDefaultAddressWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class CustomerAddressServiceProvider
 *
 * @package Gambio\Admin\Modules\CustomerAddress
 * @codeCoverageIgnore
 */
class CustomerAddressServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerAddressFactory::class,
            CustomerDefaultAddressWriteServiceInterface::class,
            CustomerDefaultAddressReadServiceInterface::class,
            CustomerDefaultAddressRepositoryInterface::class,
            CustomerCountryTranslationRepositoryInterface::class,
            FetchCustomerDefaultAddress::class,
            UpdateCustomersDefaultAddress::class,
            CustomerAddressRepositoryInterface::class,
            DeleteAddressesAfterDeletionOfACustomerEventListener::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerAddressReader::class)->addArgument(Connection::class);
        $this->application->registerShared(CustomerAddressWriter::class)->addArgument(Connection::class);
        $this->application->registerShared(CustomerAddressMapper::class)
            ->addArgument(CustomerCountryTranslationRepositoryInterface::class)
            ->addArgument(CustomerAddressFactory::class);
        $this->application->registerShared(CustomerAddressFactory::class);
        
        $this->application->registerShared(CustomerDefaultAddressRepositoryInterface::class,
                                           CustomerDefaultAddressRepository::class)
            ->addArgument(CustomerAddressReader::class)
            ->addArgument(CustomerAddressWriter::class)
            ->addArgument(CustomerAddressMapper::class);
        
        $this->application->registerShared(CustomerDefaultAddressWriteServiceInterface::class,
                                           CustomerDefaultAddressWriteService::class)
            ->addArgument(CustomerDefaultAddressRepositoryInterface::class)
            ->addArgument(CustomerAddressFactory::class);
        
        $this->application->registerShared(CustomerDefaultAddressReadServiceInterface::class,
                                           CustomerDefaultAddressReadService::class)
            ->addArgument(CustomerDefaultAddressRepositoryInterface::class)
            ->addArgument(CustomerAddressFactory::class);
        
        $this->application->registerShared(FetchCustomerDefaultAddress::class)
            ->addArgument(CustomerDefaultAddressReadServiceInterface::class);
        
        $this->application->registerShared(UpdateCustomersDefaultAddress::class)
            ->addArgument(CustomerDefaultAddressReadServiceInterface::class)
            ->addArgument(CustomerDefaultAddressWriteServiceInterface::class)
            ->addArgument(CustomerAddressFactory::class);
        
        $this->application->registerShared(CustomerCountryTranslationRepositoryInterface::class,
                                           CustomerAddressCountryTranslationRepository::class)
            ->addArgument(TextManager::class)
            ->addArgument(CustomerAddressFactory::class);
        
        $this->application->registerShared(CustomerAddressRepositoryInterface::class, CustomerAddressRepository::class)
            ->addArgument(CustomerAddressReader::class)
            ->addArgument(CustomerAddressWriter::class)
            ->addArgument(CustomerAddressMapper::class);
        
        $this->application->registerShared(DeleteAddressesAfterDeletionOfACustomerEventListener::class)
            ->addArgument(CustomerAddressRepositoryInterface::class)
            ->addArgument(CustomerAddressFactory::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->attachEventListener(CustomerDeleted::class,
                                                DeleteAddressesAfterDeletionOfACustomerEventListener::class);
    }
}