<?php
/*--------------------------------------------------------------
   ShoppingCartWriteService.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services;

interface ShoppingCartWriteService
{
    /**
     * Empties the Shopping Cart for a given Customer
     */
    public function removeShoppingCart(int $customerId): void;
}