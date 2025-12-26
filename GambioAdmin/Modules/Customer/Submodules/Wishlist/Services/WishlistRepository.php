<?php
/*--------------------------------------------------------------
   WishlistRepository.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services;

use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Wishlist;

interface WishlistRepository
{
    /**
     * Get the Wishlist for a given user.
     */
    public function getWishlist(CustomerId $customerId, int $languageId): Wishlist;
    
    
    /**
     * Remove the Wishlist for a given user.
     */
    public function removeWishlist(CustomerId $customerId): void;
}