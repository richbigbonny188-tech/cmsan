<?php
/*--------------------------------------------------------------
   CustomerOrderServiceProvider.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Submodules\Orders\App\Actions\JSON\FetchAllCustomerOrdersActions;
use Gambio\Admin\Modules\Customer\Submodules\Orders\App\CustomerOrderReadService;
use Gambio\Admin\Modules\Customer\Submodules\Orders\App\CustomerOrderRepository;
use Gambio\Admin\Modules\Customer\Submodules\Orders\App\Data\CustomerOrderMapper;
use Gambio\Admin\Modules\Customer\Submodules\Orders\App\Data\CustomerOrderReader;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderFactory;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderReadService as CustomerOrderReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderRepository as CustomerOrderRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class CustomerOrderServiceProvider
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders
 * @codeCoverageIgnore
 */
class CustomerOrderServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerOrderReadServiceInterface::class,
            FetchAllCustomerOrdersActions::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerOrderFactory::class);
        $this->application->registerShared(CustomerOrderMapper::class)->addArgument(TextManager::class);
        $this->application->registerShared(CustomerOrderReader::class)->addArgument(Connection::class)->addArgument(UserPreferences::class);
        
        $this->application->registerShared(CustomerOrderRepositoryInterface::class, CustomerOrderRepository::class)
            ->addArgument(CustomerOrderMapper::class)
            ->addArgument(CustomerOrderReader::class);
        
        $this->application->registerShared(CustomerOrderReadServiceInterface::class, CustomerOrderReadService::class)
            ->addArgument(CustomerOrderFactory::class)
            ->addArgument(CustomerOrderRepositoryInterface::class);
        
        $this->application->registerShared(FetchAllCustomerOrdersActions::class)->addArgument(CustomerOrderReadServiceInterface::class);
    }
}