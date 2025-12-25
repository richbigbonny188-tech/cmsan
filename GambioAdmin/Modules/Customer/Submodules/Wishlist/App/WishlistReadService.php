<?php
/*--------------------------------------------------------------
   WishlistReadService.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\App;

use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Wishlist;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistFactory;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistReadService as WishlistReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistRepository;

class WishlistReadService implements WishlistReadServiceInterface
{
    /**
     * @var WishlistFactory
     */
    private WishlistFactory $factory;
    
    /**
     * @var WishlistRepository
     */
    private WishlistRepository $repository;
    
    
    /**
     * Constructor
     */
    public function __construct(WishlistFactory $factory, WishlistRepository $repository)
    {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * Get the Wishlist for a given Customer ID
     */
    public function getWishlist(int $customerId, int $languageId): Wishlist
    {
        return $this->repository->getWishlist($this->factory->createCustomerId($customerId), $languageId);
    }
}