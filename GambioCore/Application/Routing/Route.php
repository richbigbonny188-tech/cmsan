<?php
/* --------------------------------------------------------------
   Route.php 2020-10-19
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
 * Interface Route
 *
 * @package Gambio\Core\Application\Routing
 */
interface Route
{
    /**
     * Get route pattern
     *
     * @return string
     */
    public function getPattern(): string;
    
    
    /**
     * Get route callable
     *
     * @return callable|string
     */
    public function getCallable();
    
    
    /**
     * Set route callable
     *
     * @param callable|string $callable
     *
     * @return self
     */
    public function setCallable($callable): Route;
    
    
    /**
     * Get route name
     *
     * @return null|string
     */
    public function getName(): ?string;
    
    
    /**
     * Set route name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): Route;
    
    
    /**
     * Retrieve a specific route argument
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function getArgument(string $name, ?string $default = null): ?string;
    
    
    /**
     * Get route arguments
     *
     * @return string[]
     */
    public function getArguments(): array;
    
    
    /**
     * Set a route argument
     *
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public function setArgument(string $name, string $value): Route;
    
    
    /**
     * Replace route arguments
     *
     * @param string[] $arguments
     *
     * @return self
     */
    public function setArguments(array $arguments): Route;
    
    
    /**
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware): Route;
}