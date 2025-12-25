<?php
/* -----------------------------------------------------------------------------
   EventListenerProvider.php 2020-03-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -----------------------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Event;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Class EventListenerProvider
 *
 * @package Gambio\Core\Event
 */
class EventListenerProvider implements ListenerProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    
    /**
     * @var string[][]
     */
    private $listeners = [];
    
    
    /**
     * EventListenerProvider constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    
    /**
     * Registers an event listener for a specific event class.
     *
     * @param string $eventClass
     * @param string $listener Full qualified class name of the listener, which must be available by the DI container.
     *
     * @return $this
     */
    public function attachListener(string $eventClass, string $listener): self
    {
        if (!array_key_exists($eventClass, $this->listeners)) {
            $this->listeners[$eventClass] = [];
        }
        
        $this->listeners[$eventClass][] = $listener;
        $this->listeners[$eventClass]   = array_unique($this->listeners[$eventClass]);
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventClass = get_class($event);
        $array      = $this->listeners[$eventClass] ?? [];
        
        return array_map(function ($listener) {
            return $this->container->get($listener);
        },
            $array);
    }
}