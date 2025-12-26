<?php
/*--------------------------------------------------------------
   ShoppingCartServiceProvider.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Actions\GetShoppingCartAction;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Actions\RemoveShoppingCartAction;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\CustomerCartRepository;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data\CustomerCartMapper;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data\ShoppingCartReader;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data\ShoppingCartWriter;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\ShoppingCartReadService;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\ShoppingCartWriteService;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartFactory;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartReadService as ShoppingCartReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartRepository as ShoppingCartRepositoryInterface;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartWriteService as ShoppingCartWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Cache\Services\CacheFactory;

/**
 * Class ShoppingCartServiceProvider
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\ShoppingCart
 * @codeCoverageIgnore
 */
class ShoppingCartServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ShoppingCartFactory::class,
            ShoppingCartReadServiceInterface::class,
            ShoppingCartWriteServiceInterface::class,
            ShoppingCartRepositoryInterface::class,
            GetShoppingCartAction::class,
            RemoveShoppingCartAction::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        // Factory
        $this->application->registerShared(ShoppingCartFactory::class);
        
        // Reader - Writer - Mapper
        $this->application->registerShared(ShoppingCartReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ShoppingCartWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(CacheFactory::class);
        $this->application->registerShared(CustomerCartMapper::class)->addArgument(ShoppingCartFactory::class);
        
        // Services - Repository
        $this->application->registerShared(ShoppingCartRepositoryInterface::class, CustomerCartRepository::class)
            ->addArgument(ShoppingCartReader::class)
            ->addArgument(ShoppingCartWriter::class)
            ->addArgument(CustomerCartMapper::class);
        $this->application->registerShared(ShoppingCartWriteServiceInterface::class, ShoppingCartWriteService::class)
            ->addArgument(ShoppingCartFactory::class)
            ->addArgument(ShoppingCartRepositoryInterface::class);
        $this->application->registerShared(ShoppingCartReadServiceInterface::class, ShoppingCartReadService::class)
            ->addArgument(ShoppingCartFactory::class)
            ->addArgument(ShoppingCartRepositoryInterface::class);
        
        // Actions
        $this->application->registerShared(GetShoppingCartAction::class)
            ->addArgument(ShoppingCartReadServiceInterface::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(RemoveShoppingCartAction::class)
            ->addArgument(ShoppingCartWriteServiceInterface::class);
    }
}