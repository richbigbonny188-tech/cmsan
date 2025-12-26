<?php
/*--------------------------------------------------------------
   ItemTitles.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems;

use Gambio\Admin\Modules\StatisticsOverview\Model\Features\TitleCollection;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem\ItemTitle;

/**
 * Class representing a collection of titles for a dropdown item.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems
 */
class ItemTitles extends TitleCollection
{
    /**
     * Create instance.
     *
     * @param ItemTitle ...$titles Values.
     *
     * @return ItemTitles Instance.
     */
    public static function create(ItemTitle ...$titles): self
    {
        return new self($titles);
    }
}