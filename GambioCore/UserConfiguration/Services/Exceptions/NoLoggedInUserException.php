<?php
/* --------------------------------------------------------------
   NoLoggedInUserException.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Services\Exceptions;

use Exception;

/**
 * Class NoLoggedInUserException
 *
 * @package Gambio\Core\UserConfiguration\Services\Exceptions
 * @codeCoverageIgnore
 */
class NoLoggedInUserException extends Exception
{
    /**
     * @return NoLoggedInUserException
     */
    public static function forThisSession(): NoLoggedInUserException
    {
        return new self('The current session doesn\'t have a logged in user and therefore, this action isn\'t allowed.');
    }
}