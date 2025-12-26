<?php
/*--------------------------------------------------------------
   ShoppingCartRepository.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ShoppingCart;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;

interface ShoppingCartRepository
{
    /**
     * Returns the Shopping Cart for a given Customer
     *
     * @param CustomerId $customerId
     * @param int        $languageId
     *
     * @return ShoppingCart
     */
    public function getShoppingCart(CustomerId $customerId, int $languageId): ShoppingCart;
    
    
    /**
     * Empties the Shopping Cart for a given Customer
     */
    public function removeShoppingCart(CustomerId $customerId): void;
}