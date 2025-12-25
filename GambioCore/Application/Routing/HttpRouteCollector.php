<?php
/* --------------------------------------------------------------
   HttpRouteCollector.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Routing;

use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Class HttpRouteCollector
 *
 * @package Gambio\Core\Application\Routing
 * @codeCoverageIgnore
 */
class HttpRouteCollector implements RouteCollector
{
    /**
     * @var RouteCollectorProxyInterface
     */
    private $internal;
    
    /**
     * @var array<string, RouteInterface>
     */
    private $routes;
    
    /**
     * @var array<string, RouteGroupInterface>
     */
    private $groups;
    
    
    /**
     * HttpRouteCollector constructor.
     *
     * @param RouteCollectorProxyInterface $internal
     */
    public function __construct(RouteCollectorProxyInterface $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getDefinedRoute(string $pattern, string $method): ?Route
    {
        $route = $this->routes[strtolower($method) . '|' . $pattern] ?? null;
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getDefinedRouteGroup(string $pattern): ?RouteGroup
    {
        $routeGroup = $this->groups[$pattern] ?? null;
        
        return new HttpRouteGroup($routeGroup);
    }
    
    
    /**
     * @inheritDoc
     */
    public function get(string $pattern, $callable): Route
    {
        $route = $this->internal->get($pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function post(string $pattern, $callable): Route
    {
        $route = $this->internal->post($pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function put(string $pattern, $callable): Route
    {
        $route = $this->internal->put($pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function patch(string $pattern, $callable): Route
    {
        $route = $this->internal->patch($pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete(string $pattern, $callable): Route
    {
        $route = $this->internal->delete($pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function options(string $pattern, $callable): Route
    {
        $route = $this->internal->options($pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function any(string $pattern, $callable): Route
    {
        $route = $this->internal->any($pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function map(array $methods, string $pattern, $callable): Route
    {
        $route = $this->internal->map($methods, $pattern, $callable);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function redirect(string $from, $to, int $status = 302): Route
    {
        $route = $this->internal->redirect($from, $to, $status);
        $this->storeDefinedRoute($route);
        
        return new HttpRoute($route);
    }
    
    
    /**
     * @inheritDoc
     */
    public function group(string $pattern, $callable): RouteGroup
    {
        $routeGroup = $this->internal->group($pattern, $callable);
        $this->storeDefinedRouteGroup($routeGroup);
        
        return new HttpRouteGroup($routeGroup);
    }
    
    
    /**
     * Stores a defined route internally.
     *
     * @param RouteInterface $route
     */
    private function storeDefinedRoute(RouteInterface $route): void
    {
        foreach ($route->getMethods() as $method) {
            $this->routes[strtolower($method) . '|' . $route->getPattern()] = $route;
        }
    }
    
    
    /**
     * Stores a defined route group internally.
     *
     * @param RouteGroupInterface $group
     */
    private function storeDefinedRouteGroup(RouteGroupInterface $group): void
    {
        $this->groups[$group->getPattern()] = $group;
    }
}