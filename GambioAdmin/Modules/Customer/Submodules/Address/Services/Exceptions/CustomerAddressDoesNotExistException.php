<?php
/*--------------------------------------------------------------
   CustomerAddressDoesNotExistException.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;

/**
 * Class CustomerAddressDoesNotExistException
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services\Exceptions
 */
class CustomerAddressDoesNotExistException extends Exception
{
    /**
     * @param CustomerId $customerId
     *
     * @return CustomerAddressDoesNotExistException
     */
    public static function forCustomerId(CustomerId $customerId): CustomerAddressDoesNotExistException
    {
        $message = 'No address associated with the customer ID "%s"';
        $message = sprintf($message, $customerId->value());
        
        return new self($message, 1);
    }
}