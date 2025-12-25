<?php
/* --------------------------------------------------------------
   JsonWebTokenAuthenticator.php 2020-04-16
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
use Gambio\Core\Auth\JsonWebTokenAuthenticator;
use Gambio\Core\Auth\UserId;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class JsonWebTokenAuthenticator
 *
 * @package Gambio\Core\Auth
 */
class BearerRequestAuthenticator implements WebRequestAuthenticator
{
    /**
     * @var JsonWebTokenAuthenticator
     */
    private $jsonWebTokenAuthenticator;
    
    
    /**
     * BasicRequestAuthenticator constructor.
     *
     * @param JsonWebTokenAuthenticator $jsonWebTokenAuthenticator
     */
    public function __construct(JsonWebTokenAuthenticator $jsonWebTokenAuthenticator)
    {
        $this->jsonWebTokenAuthenticator = $jsonWebTokenAuthenticator;
    }
    
    
    /**
     * @inheritDoc
     */
    public function authenticateWebRequest(ServerRequestInterface $request): UserId
    {
        $server = $request->getServerParams();
        if (empty($server['HTTP_AUTHORIZATION']) || strpos($server['HTTP_AUTHORIZATION'], 'Bearer ') !== 0) {
            throw new AuthenticationException('Invalid HTTP authorization header.');
        }
        
        [$bearer, $token] = explode(' ', $server['HTTP_AUTHORIZATION']);
        if (count(explode('.', $token)) !== 3) {
            throw new AuthenticationException('Invalid JSON web token provided.');
        }
        
        return $this->jsonWebTokenAuthenticator->authenticate($token);
    }
}