<?php
/* --------------------------------------------------------------
   UserAuthenticatorInterface.php 2020-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth;

use Gambio\Core\Auth\Exceptions\AuthenticationException;

/**
 * Interface UserAuthenticatorInterface
 *
 * @package Gambio\Core\Auth
 */
interface UserAuthenticator
{
    /**
     * Authenticates a user based on the provided email and password and returns his ID.
     *
     * @param string $email
     * @param string $password
     *
     * @return UserId
     *
     * @throws AuthenticationException If user can not be authenticated.
     */
    public function authenticate(string $email, string $password): UserId;
}