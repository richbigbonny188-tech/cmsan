<?php
/*--------------------------------------------------------------
   CustomerHistoryServiceProvider.php 2023-06-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\App\CustomerProductRepository;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Actions\JSON\FetchAllCustomerHistoryEntriesAction;
use Gambio\Admin\Modules\Customer\Submodules\History\App\CustomerHistoryReadService;
use Gambio\Admin\Modules\Customer\Submodules\History\App\CustomerHistoryRepository;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\CustomerHistoryMapper;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers\CustomerHistoryCartReader;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers\CustomerHistoryCustomerReader;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers\CustomerHistoryNewsletterReader;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers\CustomerHistoryOrderReader;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers\CustomerHistoryReviewReader;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers\CustomerHistoryVoucherReader;
use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers\CustomerHistoryWishlistReader;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryFactory;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReadService as CustomerHistoryReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryRepository as CustomerHistoryRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class CustomerHistoryServiceProvider
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue
 * @codeCoverageIgnore
 */
class CustomerHistoryServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @return array
     */
    private function customerHistoryReaderClassNames(): array
    {
        return [
            CustomerHistoryCartReader::class,
            CustomerHistoryCustomerReader::class,
            CustomerHistoryNewsletterReader::class,
            CustomerHistoryOrderReader::class,
            CustomerHistoryReviewReader::class,
            CustomerHistoryVoucherReader::class,
            CustomerHistoryWishlistReader::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerHistoryReadServiceInterface::class,
            CustomerHistoryRepositoryInterface::class,
            FetchAllCustomerHistoryEntriesAction::class,
            ...$this->customerHistoryReaderClassNames(),
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerHistoryFactory::class);
        $this->application->registerShared(CustomerHistoryMapper::class);
        
        $this->application->registerShared(CustomerHistoryRepositoryInterface::class, CustomerHistoryRepository::class)
            ->addArgument(CustomerHistoryMapper::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(CustomerHistoryReadServiceInterface::class,
                                           CustomerHistoryReadService::class)
            ->addArgument(CustomerHistoryRepositoryInterface::class)
            ->addArgument(CustomerHistoryFactory::class);
        
        $this->application->registerShared(FetchAllCustomerHistoryEntriesAction::class)
            ->addArgument(CustomerHistoryReadServiceInterface::class)
            ->addArgument(CustomerProductRepository::class)
            ->addArgument(UserPreferences::class);
        
        array_map(fn(string $class) => $this->application->registerShared($class)
            ->addArgument(Connection::class)
            ->addArgument(CustomerHistoryFactory::class), $this->customerHistoryReaderClassNames());
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        array_map(fn(string $class) => $this->application->inflect(CustomerHistoryRepositoryInterface::class)
            ->invokeMethod('registerCustomerHistoryReader', [$class]), $this->customerHistoryReaderClassNames());
    }
}