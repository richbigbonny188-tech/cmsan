<?php
/*--------------------------------------------------------------
   MapDataFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\MapData\MapDataItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem\ItemTitle;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem\ItemValue;

/**
 * Class representing a map data factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory
 */
class MapDataFactory
{
    /**
     * Return map data.
     *
     * @param MapDataItems $mapItems Items.
     *
     * @return MapData Map data.
     */
    public function createMapData(MapDataItems $mapItems): MapData
    {
        return MapData::create($mapItems);
    }
    
    
    /**
     * Return items.
     *
     * @param MapDataItem ...$items Items.
     *
     * @return MapDataItems Items.
     */
    public function createItems(MapDataItem ...$items): MapDataItems
    {
        return MapDataItems::create(...$items);
    }
    
    
    /**
     * Return item.
     *
     * @param ItemTitle $title Title.
     * @param ItemValue $value Value.
     *
     * @return MapDataItem Item.
     */
    public function createItem(ItemTitle $title, ItemValue $value): MapDataItem
    {
        return MapDataItem::create($title, $value);
    }
    
    
    /**
     * Return item title.
     *
     * @param string $title Value.
     *
     * @return ItemTitle Title.
     */
    public function createItemTitle(string $title): ItemTitle
    {
        return ItemTitle::create($title);
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