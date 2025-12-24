<?php
/* --------------------------------------------------------------
   AuthenticationMiddleware.php 2022-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Middleware;

use Doctrine\DBAL\Exception\DriverException;
use Gambio\Api\Application\Auth\Interfaces\WebRequestAuthenticationService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Http\ServerRequest;

/**
 * Class AuthenticationMiddleware
 *
 * @package Gambio\Api\Application\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    
    /**
     * @var WebRequestAuthenticationService
     */
    private $authenticationService;
    
    
    /**
     * AuthenticationMiddleware constructor.
     *
     * @param ResponseFactoryInterface        $responseFactory
     * @param WebRequestAuthenticationService $authenticationService
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        WebRequestAuthenticationService $authenticationService
    ) {
        $this->responseFactory       = $responseFactory;
        $this->authenticationService = $authenticationService;
    }
    
    
    /**
     * @param ServerRequestInterface|ServerRequest $request
     * @param RequestHandlerInterface              $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            if ($this->authenticationService->authenticateWebRequest($request) === false) {
                return $this->responseFactory->createResponse(401)
                    ->withHeader('WWW-Authenticate', 'Basic realm="Gambio API v3 Login"')
                    ->withJson(['error' => 'Not authenticated.']);
            }
        } catch (DriverException $exception) {
            return $this->responseFactory->createResponse(503)
                ->withHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 5)))
                ->withJson(['error' => 'Database error! ' . get_class($exception)]);
        }
        
        return $handler->handle($request);
    }
}