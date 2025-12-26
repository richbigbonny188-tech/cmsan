<?php
/* --------------------------------------------------------------
   InvalidPasswordFormatException.php 2022-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services\Exceptions;

use Exception;

/**
 * Class InvalidPasswordFormatException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class InvalidPasswordFormatException extends Exception
{
    /**
     * @param int $minLength
     *
     * @return InvalidPasswordFormatException
     */
    public static function becauseOfMinLength(int $minLength): InvalidPasswordFormatException
    {
        return new self('Given password is to short and need to be at least ' . $minLength . ' characters long.');
    }
}