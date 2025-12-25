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

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Actions\GetWishlistAction;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Actions\RemoveWishlistAction;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\CustomerWishlistRepository;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data\CustomerWishlistMapper;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data\WishlistReader;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data\WishlistWriter;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\WishlistReadService;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\WishlistWriteService;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistFactory;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistReadService as WishlistReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistRepository as WishlistRepositoryInterface;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistWriteService as WishlistWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Cache\Services\CacheFactory;

/**
 * Class ShoppingCartServiceProvider
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Wishlist
 * @codeCoverageIgnore
 */
class WishlistServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            WishlistFactory::class,
            WishlistReadServiceInterface::class,
            WishlistWriteServiceInterface::class,
            WishlistRepositoryInterface::class,
            GetWishlistAction::class,
            RemoveWishlistAction::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        
        // Factory
        $this->application->registerShared(WishlistFactory::class);
        
        // Reader - Writer - Mapper
        $this->application->registerShared(WishlistReader::class)->addArgument(Connection::class);
        $this->application->registerShared(WishlistWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(CacheFactory::class);
        $this->application->registerShared(CustomerWishlistMapper::class)->addArgument(WishlistFactory::class);
        
        // Services - Repository
        $this->application->registerShared(WishlistRepositoryInterface::class, CustomerWishlistRepository::class)
            ->addArgument(WishlistReader::class)
            ->addArgument(WishlistWriter::class)
            ->addArgument(CustomerWishlistMapper::class);
        $this->application->registerShared(WishlistWriteServiceInterface::class, WishlistWriteService::class)
            ->addArgument(WishlistFactory::class)
            ->addArgument(WishlistRepositoryInterface::class);
        $this->application->registerShared(WishlistReadServiceInterface::class, WishlistReadService::class)
            ->addArgument(WishlistFactory::class)
            ->addArgument(WishlistRepositoryInterface::class);
        
        // Actions
        $this->application->registerShared(GetWishlistAction::class)->addArgument(WishlistReadServiceInterface::class)->addArgument(UserPreferences::class);
        $this->application->registerShared(RemoveWishlistAction::class)
            ->addArgument(WishlistWriteServiceInterface::class);
    }
}