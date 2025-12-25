<?php
/*--------------------------------------------------------------
   CustomerWishlistRepository.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\App;

use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data\CustomerWishlistMapper;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data\WishlistReader;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data\WishlistWriter;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Wishlist;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistRepository as WishlistRepositoryInterface;

class CustomerWishlistRepository implements WishlistRepositoryInterface
{
    /**
     * @var WishlistReader
     */
    private WishlistReader $reader;
    
    /**
     * @var WishlistWriter
     */
    private WishlistWriter $writer;
    
    /**
     * @var CustomerWishlistMapper
     */
    private CustomerWishlistMapper $mapper;
    
    
    /**
     * Constructor
     */
    public function __construct(WishlistReader $reader, WishlistWriter $writer, CustomerWishlistMapper $mapper)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns the Wishlist for a given user
     */
    public function getWishlist(CustomerId $customerId, int $languageId): Wishlist
    {
        $wishlistData = $this->reader->getWishlist($customerId, $languageId);
        
        return $this->mapper->mapWishlist($wishlistData, $customerId);
    }
    
    
    /**
     * Deleted the Wishlist for a given user
     */
    public function removeWishlist(CustomerId $customerId): void
    {
        $this->writer->deleteWishlist($customerId);
    }
}