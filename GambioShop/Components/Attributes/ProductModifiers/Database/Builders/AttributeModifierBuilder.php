<?php
/*--------------------------------------------------------------------------------------------------
    AttributeModifierBuilder.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);
namespace Gambio\Shop\Attributes\ProductModifiers\Database\Builders;

use Gambio\Shop\Attributes\ProductModifiers\Database\AttributeModifier;
use Gambio\Shop\ProductModifiers\Modifiers\Builders\AbstractModifierBuilder;
use Gambio\Shop\ProductModifiers\Modifiers\ModifierInterface;

/**
 * Class AttributeModifierBuilder
 * @package Gambio\Shop\Attributes\ProductModifiers\Database\Builders
 */
class AttributeModifierBuilder extends AbstractModifierBuilder
{
    
    /**
     * @inheritDoc
     */
    protected function createInstance(): ModifierInterface
    {
        return new AttributeModifier($this->id, $this->info, $this->name, $this->additionalInfo, $this->selected, $this->selectable);
    }

}