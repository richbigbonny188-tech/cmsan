<?php
/* --------------------------------------------------------------
   Application.php 2022-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application;

use Gambio\Core\Application\DependencyInjection\Abstraction\LeagueContainer;
use Gambio\Core\Application\DependencyInjection\Container;
use Gambio\Core\Application\DependencyInjection\Definition;
use Gambio\Core\Application\DependencyInjection\Inflector;
use Gambio\Core\Application\DependencyInjection\Registry;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Core\Event\PrioritizingEventListenerProvider;
use Psr\Container\ContainerInterface;

/**
 * Class Application
 *
 * @package Gambio\Core\Application
 */
class Application implements ContainerInterface, Registry
{
    public const VERSION = 'v4.9.4.1';
    
    /**
     * @var Container
     */
    private $container;
    
    /**
     * @var EventListenerProvider|null
     */
    private $eventListenerProvider;
    
    /**
     * @var PrioritizingEventListenerProvider|null
     */
    private $prioritisedEventListenerProvider;
    
    
    /**
     * Application constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    
    /**
     * @param Kernel       $kernel
     * @param Bootstrapper $bootstrapper
     */
    public static function main(Kernel $kernel, Bootstrapper $bootstrapper): void
    {
        $app = new static(LeagueContainer::create());
        $kernel->bootstrap($app, $bootstrapper);
        
        $kernel->run();
    }
    
    
    /**
     * @param string $serviceProvider
     */
    public function registerProvider(string $serviceProvider): void
    {
        $this->container->registerProvider(new $serviceProvider($this));
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(string $id, $concrete = null): Definition
    {
        return $this->container->register($id, $concrete);
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerShared(string $id, $concrete = null): Definition
    {
        return $this->container->registerShared($id, $concrete);
    }
    
    
    /**
     * @inheritDoc
     */
    public function inflect(string $type, callable $callback = null): Inflector
    {
        return $this->container->inflect($type, $callback);
    }
    
    
    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
    
    
    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->container->has($id);
    }
    
    
    /**
     * @param string $eventClass
     * @param string $listener
     */
    public function attachEventListener(string $eventClass, string $listener): void
    {
        if (null === $this->eventListenerProvider && $this->container->has(EventListenerProvider::class)) {
            $this->eventListenerProvider = $this->container->get(EventListenerProvider::class);
        }
        if ($this->eventListenerProvider) {
            $this->eventListenerProvider->attachListener($eventClass, $listener);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function attachPrioritisedEventListener(string $eventClass, string $listener): void
    {
        if (null === $this->prioritisedEventListenerProvider
            && $this->container->has(PrioritizingEventListenerProvider::class)) {
            $this->prioritisedEventListenerProvider = $this->container->get(PrioritizingEventListenerProvider::class);
        }
        if ($this->prioritisedEventListenerProvider) {
            $this->prioritisedEventListenerProvider->attachListener($eventClass, $listener);
        }
    }
}