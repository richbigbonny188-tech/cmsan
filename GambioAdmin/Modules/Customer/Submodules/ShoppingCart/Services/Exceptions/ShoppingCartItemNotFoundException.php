<?php
/*--------------------------------------------------------------
   ShoppingCartItemNotFoundException.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\Exceptions;

use Exception;

/**
 * @codeCoverageIgnore
 */
class ShoppingCartItemNotFoundException extends Exception
{
    /**
     * Returns a new Exception describing a given Customer ID and Item ID
     */
    public static function forItem(int $customerId, int $itemId): self
    {
        return new self("Shopping Cart Item \"" . $itemId . "\" not Found for Customer \"" . $customerId . "\"");
    }
}