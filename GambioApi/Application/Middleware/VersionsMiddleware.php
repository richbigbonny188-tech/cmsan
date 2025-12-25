<?php
/* --------------------------------------------------------------
   VersionsMiddleware.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Middleware;

use Gambio\Api\Application\GambioApiBootstrapper;
use Gambio\Core\Application\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class VersionsMiddleware
 *
 * @package Gambio\Api\Application\Middleware
 */
class VersionsMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        
        return $response->withHeader('X-Shop-Version', 'v' . ltrim(Application::VERSION, 'v'))
            ->withHeader('X-API-Version', GambioApiBootstrapper::VERSION);
    }
}