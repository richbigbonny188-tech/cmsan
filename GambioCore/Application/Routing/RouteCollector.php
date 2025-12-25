<?php
/* --------------------------------------------------------------
   RouteCollector.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Routing;

use Psr\Http\Message\UriInterface;

/**
 * Interface RouteCollector
 *
 * @package Gambio\Core\Application\Routing
 */
interface RouteCollector
{
    /**
     * Returns a previous defined route by its given pattern and method.
     *
     * @param string $pattern
     * @param string $method
     *
     * @return Route|null
     */
    public function getDefinedRoute(string $pattern, string $method): ?Route;
    
    
    /**
     * Returns a previous defined route group by its given pattern.
     *
     * @param string $pattern
     *
     * @return RouteGroup|null
     */
    public function getDefinedRouteGroup(string $pattern): ?RouteGroup;
    
    
    /**
     * Add GET route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function get(string $pattern, $callable): Route;
    
    
    /**
     * Add POST route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function post(string $pattern, $callable): Route;
    
    
    /**
     * Add PUT route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function put(string $pattern, $callable): Route;
    
    
    /**
     * Add PATCH route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function patch(string $pattern, $callable): Route;
    
    
    /**
     * Add DELETE route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function delete(string $pattern, $callable): Route;
    
    
    /**
     * Add OPTIONS route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function options(string $pattern, $callable): Route;
    
    
    /**
     * Add route for any HTTP method
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function any(string $pattern, $callable): Route;
    
    
    /**
     * Add route with multiple methods
     *
     * @param string[]        $methods  Numeric array of HTTP method names
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return Route
     */
    public function map(array $methods, string $pattern, $callable): Route;
    
    
    /**
     * Add a route that sends an HTTP redirect
     *
     * @param string              $from
     * @param string|UriInterface $to
     * @param int                 $status
     *
     * @return Route
     */
    public function redirect(string $from, $to, int $status = 302): Route;
    
    
    /**
     * Route Groups
     *
     * This method accepts a route pattern and a callback. All route
     * declarations in the callback will be prepended by the group(s)
     * that it is in.
     *
     * @param string          $pattern
     * @param string|callable $callable
     *
     * @return RouteGroup
     */
    public function group(string $pattern, $callable): RouteGroup;
}