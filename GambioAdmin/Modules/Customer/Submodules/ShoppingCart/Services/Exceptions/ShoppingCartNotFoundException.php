<?php
/*--------------------------------------------------------------
   ShoppingCartNotFoundException.php 2022-09-13
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
class ShoppingCartNotFoundException extends Exception
{
    /**
     * Returns a new Exception for a given Customer ID
     */
    public static function forCustomer(int $customerId): self
    {
        return new self("No Shopping Cart Found for Customer \"" . $customerId . "\"");
    }
}