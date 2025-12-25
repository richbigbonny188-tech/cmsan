<?php
/*--------------------------------------------------------------
   GuestCustomersBalanceCantBeChangedException.php 2022-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services\Exceptions;

use Gambio\Admin\Modules\Customer\Model\Customer;

/**
 * Class GuestCustomersBalanceCantBeChangedException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class GuestCustomersBalanceCantBeChangedException extends StorageOfCustomerFailedException
{
    /**
     * @param Customer $id
     *
     * @return GuestCustomersBalanceCantBeChangedException
     */
    public static function forCustomer(Customer $id): GuestCustomersBalanceCantBeChangedException
    {
        $message = 'Customer with ID %s is a guest. Guests can\'t have a balance';
        $message = sprintf($message, $id->id());
        
        return new self($message);
    }
}