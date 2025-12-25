<?php
/*--------------------------------------------------------------------------------------------------
    OnModifierIdCreateEventInterface.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\Events\Interfaces;

use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Psr\EventDispatcher\StoppableEventInterface;

interface OnModifierIdCreateEventInterface extends StoppableEventInterface
{
    /**
     * @return mixed
     */
    public function id();
    
    
    /**
     * @return string
     */
    public function type(): string;
    
    
    /**
     * @return ModifierIdentifierInterface
     */
    public function modifierId(): ?ModifierIdentifierInterface;
    
    
    /**
     * @param ModifierIdentifierInterface $modifierId
     *
     * @return void
     */
    public function setModifierId(ModifierIdentifierInterface $modifierId): void;

    public function stop(): void;
}