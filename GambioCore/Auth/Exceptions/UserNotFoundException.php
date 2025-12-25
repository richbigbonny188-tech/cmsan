<?php
/* --------------------------------------------------------------
   UserNotFoundException.php 2019-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Exceptions;

use Exception;

/**
 * Class UserNotFoundException
 *
 * @package Gambio\Core\Auth\Exceptions
 */
class UserNotFoundException extends Exception
{
    /**
     * Return new created exception by using a provided email.
     *
     * @param string $email
     *
     * @return static
     */
    public static function forEmail(string $email): self
    {
        return new self('Could no found user with email "' . $email . '".');
    }
}