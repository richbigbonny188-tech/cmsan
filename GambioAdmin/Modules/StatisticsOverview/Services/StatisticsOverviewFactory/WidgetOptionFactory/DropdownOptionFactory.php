<?php
/*--------------------------------------------------------------
   DropdownOptionFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory;

use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems\ItemTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\OptionTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem\ItemTitle;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem\ItemValue;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\OptionId;

/**
 * Class representing a dropdown factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory
 */
class DropdownOptionFactory
{
    /**
     * Return dropdown.
     *
     * @param OptionId     $id     ID.
     * @param string       $value  Value.
     * @param OptionItems  $items  Dropdown items.
     * @param OptionTitles $titles Multilingual titles.
     *
     * @return DropdownOption
     */
    public function createDropdown(
        OptionId $id,
        string $value,
        OptionItems $items,
        OptionTitles $titles
    ): DropdownOption {
        return DropdownOption::create($id,
                                      $value,
                                      $items,
                                      $titles);
    }
    
    
    /**
     * Return dropdown items.
     *
     * @param OptionItem ...$items Dropdown items.
     *
     * @return OptionItems Dropdown items.
     */
    public function createItems(OptionItem ...$items): OptionItems
    {
        return OptionItems::create(...$items);
    }
    
    
    /**
     * Return dropdown item.
     *
     * @param ItemValue  $value  Value.
     * @param ItemTitles $titles Multilingual titles.
     *
     * @return OptionItem
     */
    public function createItem(
        ItemValue $value,
        ItemTitles $titles
    ): OptionItem {
        return OptionItem::create($value, $titles);
    }
    
    
    /**
     * Return dropdown item multilingual titles.
     *
     * @param ItemTitle ...$titles Multilingual titles.
     *
     * @return ItemTitles Multilingual titles.
     */
    public function createItemTitles(ItemTitle ...$titles): ItemTitles
    {
        return ItemTitles::create(...$titles);
    }
    
    
    /**
     * Return dropdown item title.
     *
     * @param LanguageCode $languageCode Language code.
     * @param string       $title        Title.
     *
     * @return ItemTitle
     */
    public function createItemTitle(LanguageCode $languageCode, string $title): ItemTitle
    {
        return ItemTitle::create($languageCode, $title);
    }
    
    
    /**
     * Return item value.
     *
     * @param string $value Value.
     *
     * @return ItemValue Value.
     */
    public function createValue(string $value): ItemValue
    {
        return ItemValue::create($value);
    }
}