<?php
/* -----------------------------------------------------------------------------
   PrioritizingEventListenerProvider.php 2021-06-02
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
use RuntimeException;

/**
 * Class PrioritizingEventListenerProvider
 *
 * @package Gambio\Core\Event
 */
class PrioritizingEventListenerProvider implements ListenerProviderInterface
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
     * @var PrioritizedEventListener[][]|null
     */
    private $actualListeners = null;
    
    
    /**
     * PrioritizingEventListenerProvider constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    
    /**
     * @param string $eventClass
     * @param string $listener
     *
     * @return $this
     */
    public function attachListener(string $eventClass, string $listener): self
    {
        $this->listeners[$eventClass][$listener] = $listener;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventClass = get_class($event);
        
        return array_values($this->getActualListeners($eventClass));
    }
    
    
    /**
     * @return PrioritizedEventListener[]
     */
    private function getActualListeners(string $requestedEventClass): array
    {
        if ($this->actualListeners === null) {
            foreach ($this->listeners as $eventClass => $listeners) {
                $this->actualListeners[$eventClass] = array_map(function (string $listener): PrioritizedEventListener {
                    $listener = $this->container->get($listener);
                    if (($listener instanceof PrioritizedEventListener) === false) {
                        throw new RuntimeException('Given prioritized listener "' . get_class($listener)
                                                   . '" needs to implement "' . PrioritizedEventListener::class
                                                   . '" interface.');
                    }
                    
                    return $listener;
                },
                    $listeners);
                
                uasort($this->actualListeners[$eventClass],
                    static function (PrioritizedEventListener $listener1, PrioritizedEventListener $listener2): int {
                        return $listener2->priority() - $listener1->priority();
                    });
            }
        }
        
        return $this->actualListeners[$requestedEventClass] ?? [];
    }
}