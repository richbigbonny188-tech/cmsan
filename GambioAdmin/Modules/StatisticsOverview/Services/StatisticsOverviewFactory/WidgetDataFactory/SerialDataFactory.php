<?php
/*--------------------------------------------------------------
   SerialDataFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataCategories;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataItems\ItemValues;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataCategory;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem\ItemTitle;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem\ItemValue;

/**
 * Class representing a serial data factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory
 */
class SerialDataFactory
{
    /**
     * Return serial data.
     *
     * @param SerialDataCategories $categories Categories.
     * @param SerialDataItems      $series     Series.
     *
     * @return SerialData Serial data.
     */
    public function createSerialData(
        SerialDataCategories $categories,
        SerialDataItems $series
    ): SerialData {
        return SerialData::create($categories, $series);
    }
    
    
    /**
     * Return categories.
     *
     * @param SerialDataCategory ...$categories Categories.
     *
     * @return SerialDataCategories Categories.
     */
    public function createCategories(SerialDataCategory ...$categories): SerialDataCategories
    {
        return SerialDataCategories::create(...$categories);
    }
    
    
    /**
     * Return category.
     *
     * @param string $category Category.
     *
     * @return SerialDataCategory Category.
     */
    public function createCategory(string $category): SerialDataCategory
    {
        return SerialDataCategory::create($category);
    }
    
    
    /**
     * Return items.
     *
     * @param SerialDataItem ...$items Items.
     *
     * @return SerialDataItems Items.
     */
    public function createItems(SerialDataItem ...$items): SerialDataItems
    {
        return SerialDataItems::create(...$items);
    }
    
    
    /**
     * Return item.
     *
     * @param ItemTitle  $title  Title.
     * @param ItemValues $values Values.
     *
     * @return SerialDataItem Item.
     */
    public function createItem(
        ItemTitle $title,
        ItemValues $values
    ): SerialDataItem {
        return SerialDataItem::create($title, $values);
    }
    
    
    /**
     * Return item title.
     *
     * @param string $title Title.
     *
     * @return ItemTitle Title.
     */
    public function createItemTitle(string $title): ItemTitle
    {
        return ItemTitle::create($title);
    }
    
    
    /**
     * Return item values.
     *
     * @param ItemValue ...$values Values.
     *
     * @return ItemValues Values.
     */
    public function createItemValues(ItemValue ...$values): ItemValues
    {
        return ItemValues::create(...$values);
    }
    
    
    /**
     * Return item value.
     *
     * @param float $value Value.
     *
     * @return ItemValue Value.
     */
    public function createItemValue(float $value): ItemValue
    {
        return ItemValue::create($value);
    }
}