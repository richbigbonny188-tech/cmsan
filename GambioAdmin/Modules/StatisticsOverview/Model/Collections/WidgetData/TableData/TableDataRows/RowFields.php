<?php
/*--------------------------------------------------------------
   RowFields.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataRows;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField;
use IteratorAggregate;

/**
 * Class representing a collection of table row fields.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataRows
 */
class RowFields implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var RowField[]
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
     * @param RowField ...$fields Values.
     *
     * @return RowFields Instance.
     */
    public static function create(RowField ...$fields): self
    {
        return new self($fields);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
}