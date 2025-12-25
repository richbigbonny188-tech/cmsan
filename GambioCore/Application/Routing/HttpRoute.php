<?php
/* --------------------------------------------------------------
   HttpRoute.php 2020-10-19
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
use Slim\Interfaces\RouteInterface;

/**
 * Class HttpRoute
 *
 * @package Gambio\Core\Application\Routing
 * @codeCoverageIgnore
 */
class HttpRoute implements Route
{
    /**
     * @var RouteInterface
     */
    private $internal;
    
    
    /**
     * HttpRoute constructor.
     *
     * @param RouteInterface $internal
     */
    public function __construct(RouteInterface $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPattern(): string
    {
        return $this->internal->getPattern();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCallable()
    {
        return $this->internal->getCallable();
    }
    
    
    /**
     * @inheritDoc
     */
    public function setCallable($callable): Route
    {
        $this->internal = $this->internal->setCallable($callable);
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->internal->getName();
    }
    
    
    /**
     * @inheritDoc
     */
    public function setName(string $name): Route
    {
        $this->internal = $this->internal->setName($name);
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getArgument(string $name, ?string $default = null): ?string
    {
        return $this->internal->getArgument($name, $default);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->internal->getArguments();
    }
    
    
    /**
     * @inheritDoc
     */
    public function setArgument(string $name, string $value): Route
    {
        $this->internal = $this->internal->setArgument($name, $value);
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setArguments(array $arguments): Route
    {
        $this->internal = $this->internal->setArguments($arguments);
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addMiddleware(MiddlewareInterface $middleware): Route
    {
        $this->internal = $this->internal->addMiddleware($middleware);
        
        return $this;
    }
}