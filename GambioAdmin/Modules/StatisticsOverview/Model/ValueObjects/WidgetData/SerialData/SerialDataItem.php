<?php
/*--------------------------------------------------------------
   SerialDataItem.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataItems\ItemValues;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem\ItemTitle;

/**
 * Class representing a serial item.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData
 */
class SerialDataItem
{
    /**
     * Title.
     *
     * @var ItemTitle
     */
    private $title;
    
    /**
     * Values.
     *
     * @var ItemValues
     */
    private $values;
    
    
    /**
     * Constructor.
     *
     * @param ItemTitle  $title  Title.
     * @param ItemValues $values Values.
     */
    private function __construct(ItemTitle $title, ItemValues $values)
    {
        $this->title  = $title;
        $this->values = $values;
    }
    
    
    /**
     * Create instance.
     *
     * @param ItemTitle  $title  Title.
     * @param ItemValues $values Values.
     *
     * @return SerialDataItem
     */
    public static function create(ItemTitle $title, ItemValues $values): self
    {
        return new self($title, $values);
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
     * Return values.
     *
     * @return ItemValues Values.
     */
    public function values(): ItemValues
    {
        return $this->values;
    }
}