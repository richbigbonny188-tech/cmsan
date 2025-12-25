<?php
/*--------------------------------------------------------------
   TableDataRows.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow;
use IteratorAggregate;

/**
 * Class representing a collection of table rows.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData
 */
class TableDataRows implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var TableDataRow[]
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
     * @param TableDataRow ...$rows Values.
     *
     * @return TableDataRows Instance.
     */
    public static function create(TableDataRow ...$rows): self
    {
        return new self($rows);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
}