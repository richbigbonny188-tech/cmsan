<?php
/*--------------------------------------------------------------
   MapDataItem.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem\ItemTitle;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem\ItemValue;

/**
 * Class representing a map item.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData
 */
class MapDataItem
{
    /**
     * Title.
     *
     * @var ItemTitle
     */
    private $title;
    
    /**
     * Value.
     *
     * @var ItemValue
     */
    private $value;
    
    
    /**
     * Constructor.
     *
     * @param ItemTitle $title Title.
     * @param ItemValue $value Value.
     */
    private function __construct(ItemTitle $title, ItemValue $value)
    {
        $this->title = $title;
        $this->value = $value;
    }
    
    
    /**
     * Create instance.
     *
     * @param ItemTitle $title Title.
     * @param ItemValue $value Value.
     *
     * @return MapDataItem Instance.
     */
    public static function create(ItemTitle $title, ItemValue $value): self
    {
        return new self($title, $value);
    }
    
    
    /**
     * Return title.
     *
     * @return ItemTitle Title.
     */
    public function title(): ItemTitle
    {
        return $this->title;
    }
    
    
    /**
     * Return value.
     *
     * @return ItemValue Value.
     */
    public function value(): ItemValue
    {
        return $this->value;
    }
}