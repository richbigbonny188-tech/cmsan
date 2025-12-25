<?php
/*--------------------------------------------------------------
   ItemValues.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataItems;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem\ItemValue;
use IteratorAggregate;

/**
 * Class representing the value collection for a serial item.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataItems
 */
class ItemValues implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var ItemValue[]
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
     * @param ItemValue ...$values Values.
     *
     * @return ItemValues Instance.
     */
    public static function create(ItemValue ...$values): self
    {
        return new self($values);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }
}