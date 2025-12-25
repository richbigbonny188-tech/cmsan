<?php
/*--------------------------------------------------------------------------------------------------
    ModifierSelected.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\ValueObjects;

/**
 * Class ModifierSelected
 *
 * @package Gambio\Shop\ProductModifiers\Modifiers\ValueObjects
 */
class ModifierSelected
{
    /**
     * @var bool
     */
    private $selected;

    /**
     * ModifierSelected constructor.
     *
     * @param bool $selected
     */
    public function __construct(bool $selected)
    {
        $this->selected = $selected;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

}