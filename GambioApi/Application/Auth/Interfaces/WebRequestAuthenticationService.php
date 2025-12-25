<?php
/* --------------------------------------------------------------
   WebRequestAuthenticationService.php 2020-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Auth\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface WebRequestAuthenticationService
 *
 * @package Gambio\Core\Auth
 */
interface WebRequestAuthenticationService
{
    /**
     * Adds a web request authenticator to the service, which will be used to authenticate a web request.
     *
     * @param WebRequestAuthenticator $authenticator
     *
     * @return $this
     */
    public function addAuthenticator(WebRequestAuthenticator $authenticator): self;
    
    
    /**
     * Authenticates the provided server request.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool True, if authentication succeeded, otherwise false.
     */
    public function authenticateWebRequest(ServerRequestInterface $request): bool;
}