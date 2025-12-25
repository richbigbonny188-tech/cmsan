<?php
/*--------------------------------------------------------------------------------------------------
    ModifierSelectable.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\ValueObjects;

/**
 * Class ModifierSelectable
 *
 * @package Gambio\Shop\ProductModifiers\Modifiers\ValueObjects
 */
class ModifierSelectable
{
    /**
     * @var bool
     */
    private $active;

    /**
     * ModifierActive constructor.
     *
     * @param bool $active
     */
    public function __construct(bool $active)
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isSelectable(): bool
    {
        return $this->active;
    }

}