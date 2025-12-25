<?php
/*--------------------------------------------------------------------------------------------------
    AttributeModifier.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);
namespace Gambio\Shop\Attributes\ProductModifiers\Database;

use Gambio\Shop\Attributes\ProductModifiers\Database\Interfaces\AttributeModifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\AbstractModifier;

/**
 * Class AttributeModifier
 * @package Gambio\Shop\Attributes\Database\Modifiers
 */
class AttributeModifier extends AbstractModifier implements AttributeModifierInterface
{
    /**
     * @inheritDoc
     */
    public static function source(): string
    {
        return 'attribute';
    }
}