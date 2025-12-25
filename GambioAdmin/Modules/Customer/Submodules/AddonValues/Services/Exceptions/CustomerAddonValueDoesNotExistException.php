<?php
/* --------------------------------------------------------------
   CustomerAddonValueDoesNotExistException.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions;

use Exception;

/**
 * Class CustomerAddonValueDoesNotExistException
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services\Exceptions
 * @codeCoverageIgnore
 */
class CustomerAddonValueDoesNotExistException extends Exception
{
    /**
     * @param int    $customerId
     * @param string $key
     *
     * @return CustomerAddonValueDoesNotExistException
     */
    public static function forGivenCustomerAndKey(int $customerId, string $key): CustomerAddonValueDoesNotExistException
    {
        return new self(sprintf('Customer addon value does not exist for customer ID %s and key "%s".',
                                $customerId,
                                $key));
    }
}