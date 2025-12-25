<?php
/*--------------------------------------------------------------
   MapDataItems.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\MapData;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem;
use IteratorAggregate;

/**
 * Class representing the item collection for a map.
 *
 * @package ambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\MapData
 */
class MapDataItems implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var MapDataItem[]
     */
    private $values;
    
    
    /**
     * Constructor.
     *
     * @param array $values Values.
     */
    private function __construct(array $values)
    {
        $this->values = $values;
    }
    
    
    /**
     * Create instance.
     *
     * @param MapDataItem ...$items Values.
     *
     * @return MapDataItems Instance.
     */
    public static function create(MapDataItem ...$items): self
    {
        return new self($items);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
    
}