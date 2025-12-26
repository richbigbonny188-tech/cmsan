<?php
/*--------------------------------------------------------------------------------------------------
    OnModifierIdCreateEvent.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Modifiers\Events;

use Gambio\Shop\ProductModifiers\Modifiers\Events\Interfaces\OnModifierIdCreateEventInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;

/**
 * Class OnModifierIdCreateEvent
 * @package Gambio\Shop\ProductModifiers\Modifiers\Events
 */
class OnModifierIdCreateEvent implements OnModifierIdCreateEventInterface
{
    /**
     * @var ModifierIdentifierInterface
     */
    protected $modifierId;
    /**
     * @var bool
     */
    protected $stopped = false;
    /**
     * @var Mixed
     */
    private   $id;
    /**
     * @var string
     */
    private $type;
    
    
    /**
     * OnModifierIdCreateEvent constructor.
     *
     * @param string $type
     * @param        $id
     */
    public function __construct(string $type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }
    
    /**
     * @inheritDoc
     */
    public function id()
    {
        return $this->id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
    
    
    /**
     * @inheritDoc
     */
    public function modifierId(): ?ModifierIdentifierInterface
    {
        return $this->modifierId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setModifierId(ModifierIdentifierInterface $modifierId): void
    {
        $this->modifierId = $modifierId;
    }
    
    
    public function stop(): void
    {
        $this->stopped = true;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}