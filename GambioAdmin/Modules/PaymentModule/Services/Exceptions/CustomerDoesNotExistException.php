<?php
/*--------------------------------------------------------------
   CustomerDoesNotExistException.php 2022-01-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\Services\Exceptions;

use Exception;

/**
 * Class CustomerDoesNotExistException
 *
 * @package Gambio\Admin\Modules\PaymentModule\Services\Exceptions
 */
class CustomerDoesNotExistException extends Exception
{
    /**
     * @param int $customerId
     *
     * @return CustomerDoesNotExistException
     */
    public static function withId(int $customerId): CustomerDoesNotExistException
    {
        $message = 'No customer exists with the ID "%s"';
        
        return new self(sprintf($message, $customerId));
    }
}