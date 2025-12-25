<?php
/* --------------------------------------------------------------
 Module.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Modules;

/**
 * Interface Module
 *
 * @package Gambio\Core\Application\Modules
 */
interface Module
{
    /**
     * List of event listeners.
     *
     * The list must be a multidimensional array in the following format:
     * (Fqn means full qualified class name).
     * key = Fqn event class name
     * value = numeric, one dimensional array with fqn event listener names
     *
     * Example:
     *
     * ```php
     * use Vendor\Library\FooEvent;
     * use Vendor\Library\FooEventListenerOne;
     * use Vendor\Library\FooEventListenerTwo;
     *
     * $eventListeners = [
     *      FooEvent::class =>
     *          [
     *              FooEventListenerOne::class,
     *              FooEventListenerTwo::class,
     *          ]
     * ]
     * ```
     *
     * @return array|null
     */
    public function eventListeners(): ?array;
    
    
    /**
     * List of external dependencies.
     *
     * If the module depends on any external dependencies, the must be declared here.
     * Todo: Refine documentation.
     */
    public function dependsOn(): ?array;
    
    
    /**
     * List of additions middleware for the shop.
     *
     * The list must be a one dimensional array with fqn middleware class names.
     * (Fqn means full qualified class name).
     * Example:
     *
     * ```php
     * use Vendor\Library\MiddlewareOne;
     * use Vendor\Library\MiddlewareTwo;
     *
     * $middleware = [
     *     MiddlewareOne::class,
     *     MiddlewareTwo::class,
     * ]
     * ```
     */
    public function shopMiddleware(): ?array;
    
    
    /**
     * List of additions middleware for the admin.
     *
     * The list must be a one dimensional array with fqn middleware class names.
     * (Fqn means full qualified class name).
     * Example:
     *
     * ```php
     * use Vendor\Library\MiddlewareOne;
     * use Vendor\Library\MiddlewareTwo;
     *
     * $middleware = [
     *     MiddlewareOne::class,
     *     MiddlewareTwo::class,
     * ]
     * ```
     */
    public function adminMiddleware(): ?array;
    
    
    /**
     * List of additions middleware for the REST API v3.
     *
     * The list must be a one dimensional array with fqn middleware class names.
     * (Fqn means full qualified class name).
     * Example:
     *
     * ```php
     * use Vendor\Library\MiddlewareOne;
     * use Vendor\Library\MiddlewareTwo;
     *
     * $middleware = [
     *     MiddlewareOne::class,
     *     MiddlewareTwo::class,
     * ]
     * ```
     */
    public function apiMiddleware(): ?array;
}