<?php
/*--------------------------------------------------------------------------------------------------
    PropertyModifier.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);

namespace Gambio\Shop\Properties\ProductModifiers\Database;

use Gambio\Shop\Properties\ProductModifiers\Database\Interfaces\PropertyModifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\AbstractModifier;

/**
 * Class PropertyModifier
 * @package Gambio\Shop\Properties\ProductModifiers\Database
 */
class PropertyModifier extends AbstractModifier implements PropertyModifierInterface
{
    
    /**
     * @inheritDoc
     */
    public static function source(): string
    {
        return 'property';
    }
}