<?php
/* --------------------------------------------------------------
   RouteGroup.php 2020-10-19
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

/**
 * Interface RouteGroup
 *
 * @package Gambio\Core\Application\Routing
 */
interface RouteGroup
{
    /**
     * Add middleware to the route group
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware): RouteGroup;
}