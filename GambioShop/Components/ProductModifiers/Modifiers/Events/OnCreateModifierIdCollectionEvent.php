<?php
/*--------------------------------------------------------------------
 OnCreateModifierIdCollectionEvent.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\ProductModifiers\Modifiers\Events;

use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollection;

/**
 * Class OnCreateModifierIdCollectionEvent
 * @package Gambio\Shop\ProductModifiers\Modifiers\Events
 */
class OnCreateModifierIdCollectionEvent
{
    /**
     * @var int
     */
    protected $combisId;
    
    /**
     * @var ModifierIdentifierCollection
     */
    protected $modifierIdentifierCollection;
    
    
    /**
     * OnCreateModifierIdCollectionEvent constructor.
     *
     * @param int $combisId
     */
    public function __construct(int $combisId)
    {
        $this->combisId = $combisId;
    }
    
    
    /**
     * @return int
     */
    public function combisId(): int
    {
        return $this->combisId;
    }
    
    
    /**
     * @param ModifierIdentifierCollection $modifierIdentifierCollection
     */
    public function setModifierIdentifierCollection(ModifierIdentifierCollection $modifierIdentifierCollection): void
    {
        $this->modifierIdentifierCollection = $modifierIdentifierCollection;
    }
    
    
    /**
     * @return ModifierIdentifierCollection
     */
    public function modifierIdentifierCollection(): ModifierIdentifierCollection
    {
        return $this->modifierIdentifierCollection;
    }
}