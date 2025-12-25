<?php
/* --------------------------------------------------------------
   UserAuthenticator.php 2020-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Auth;

use Gambio\Api\Application\Auth\Interfaces\WebRequestAuthenticator;
use Gambio\Core\Auth\Exceptions\AuthenticationException;
use Gambio\Core\Auth\UserAuthenticator;
use Gambio\Core\Auth\UserId;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UserAuthenticator
 *
 * @package Gambio\Core\Auth
 */
class BasicRequestAuthenticator implements WebRequestAuthenticator
{
    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;
    
    
    /**
     * BasicRequestAuthenticator constructor.
     *
     * @param UserAuthenticator $userAuthenticator
     */
    public function __construct(UserAuthenticator $userAuthenticator)
    {
        $this->userAuthenticator = $userAuthenticator;
    }
    
    
    /**
     * @inheritDoc
     */
    public function authenticateWebRequest(ServerRequestInterface $request): UserId
    {
        $server = $request->getServerParams();
        if (empty($server['PHP_AUTH_USER']) && empty($server['PHP_AUTH_PW'])) {
            if (empty($server['HTTP_AUTHORIZATION']) && empty($server['REDIRECT_HTTP_AUTHORIZATION'])) {
                throw new AuthenticationException('Invalid HTTP authorization header.');
            }
            
            $authHeader = !empty($server['HTTP_AUTHORIZATION']) ? $server['HTTP_AUTHORIZATION'] : $server['REDIRECT_HTTP_AUTHORIZATION'];
            $authData   = base64_decode(substr($authHeader, 6));
            if (strpos($authData, ':') === false) {
                throw new AuthenticationException('Could not parse authorization header.');
            }
            [$server['PHP_AUTH_USER'], $server['PHP_AUTH_PW']] = explode(':', $authData);
        }
        
        return $this->userAuthenticator->authenticate($server['PHP_AUTH_USER'], $server['PHP_AUTH_PW']);
    }
}