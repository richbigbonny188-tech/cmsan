<?php
/*--------------------------------------------------------------
   CreationOfCustomerAddressFailedException.php 2022-09-15
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
 * Class CreationOfCustomerAddressFailedException
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services\Exceptions
 */
class CreationOfCustomerAddressFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfCustomerAddressFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfCustomerAddressFailedException
    {
        $message = 'Creation of customer address failed because of a previous Exception';
        
        return new self($message, 1, $exception);
    }
}