<?php
/*--------------------------------------------------------------
   EmailAddressIsInvalidException.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services\Exceptions;

use Exception;

/**
 * Class EmailAddressIsInvalidException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class EmailAddressIsInvalidException extends Exception
{
    /**
     * @param string $email
     *
     * @return EmailAddressIsInvalidException
     */
    public static function forEmail(string $email): EmailAddressIsInvalidException
    {
        $message = 'The given email "%s" is not valid and can\'t be used';
        $message = sprintf($message, $email);
        
        return new self($message, 2);
    }
}