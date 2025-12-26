<?php
/*--------------------------------------------------------------
   StorageOfCustomerAddressFailedException.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions;

use Exception;

/**
 * Class StorageOfCustomerAddressFailedException
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services\Exceptions
 */
class StorageOfCustomerAddressFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfCustomerAddressFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfCustomerAddressFailedException
    {
        $message = 'Storage of customer address failed because of a previous Exception';
        
        return new self($message, 1, $exception);
    }
}