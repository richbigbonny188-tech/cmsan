<?php
/*--------------------------------------------------------------------------------------------------
    GroupStatus.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Groups\ValueObjects;

/**
 * Class GroupStatus
 *
 * @package Gambio\Shop\ProductModifiers\Groups\ValueObjects
 */
class GroupStatus
{
    /**
     * @var bool
     */
    private $selectable;

    /**
     * GroupStatus constructor.
     *
     * @param bool $isSelectable
     */
    public function __construct(bool $isSelectable)
    {
        $this->selectable = $isSelectable;
    }

    /**
     * @return bool
     */
    public function isSelectable(): bool
    {
        return $this->selectable;
    }

}