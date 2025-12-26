<?php
/*--------------------------------------------------------------
   SerialItems.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem;
use IteratorAggregate;

/**
 * Class representing a serial item collection.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData
 */
class SerialDataItems implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var SerialDataItem[]
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
     * @param SerialDataItem ...$items Values.
     *
     * @return SerialDataItems Instance.
     */
    public static function create(SerialDataItem ...$items): self
    {
        return new self($items);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->values);
    }
    
}