<?php
/* --------------------------------------------------------------
   WebRequestAuthenticator.php 2020-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Auth\Interfaces;

use Gambio\Core\Auth\Exceptions\AuthenticationException;
use Gambio\Core\Auth\UserId;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface WebRequestAuthenticator
 *
 * @package Gambio\Core\Auth
 */
interface WebRequestAuthenticator
{
    /**
     * Authenticates the provided server request and returns the user ID.
     *
     * @param ServerRequestInterface $request
     *
     * @return UserId
     *
     * @throws AuthenticationException
     */
    public function authenticateWebRequest(ServerRequestInterface $request): UserId;
}