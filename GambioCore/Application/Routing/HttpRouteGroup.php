<?php
/* --------------------------------------------------------------
   HttpRouteGroup.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Routing;

use Psr\Http\Server\MiddlewareInterface;
use Slim\Interfaces\RouteGroupInterface;

/**
 * Class HttpRouteGroup
 *
 * @package Gambio\Core\Application\Routing
 * @codeCoverageIgnore
 */
class HttpRouteGroup implements RouteGroup
{
    /**
     * @var RouteGroupInterface
     */
    private $internal;
    
    
    /**
     * HttpRouteGroup constructor.
     *
     * @param RouteGroupInterface $internal
     */
    public function __construct(RouteGroupInterface $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addMiddleware(MiddlewareInterface $middleware): RouteGroup
    {
        $this->internal = $this->internal->addMiddleware($middleware);
        
        return $this;
    }
}