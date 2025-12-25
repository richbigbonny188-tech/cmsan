<?php
/*--------------------------------------------------------------
   DeletionOfCustomerAddressFailedException.php 2022-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;

/**
 * Class DeletionOfCustomerAddressFailedException
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions
 */
class DeletionOfCustomerAddressFailedException extends Exception
{
    /**
     * @param Exception         $exception
     * @param CustomerAddressId $addressId
     *
     * @return DeletionOfCustomerAddressFailedException
     */
    public static function becauseOfException(Exception $exception, CustomerAddressId $addressId): DeletionOfCustomerAddressFailedException
    {
        $message = 'Deletion of customer address ID %s failed because of a previous Exception';
        $message = sprintf($message, $addressId->value());
    
        return new self($message, 1, $exception);
    }
}