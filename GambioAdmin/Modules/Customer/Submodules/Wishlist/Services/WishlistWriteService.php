<?php
/*--------------------------------------------------------------
   WishlistWriteService.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services;

interface WishlistWriteService
{
    /**
     * Removes all Items from the Wishlist for a given user
     */
    public function removeWishlist(int $customerId): void;
}