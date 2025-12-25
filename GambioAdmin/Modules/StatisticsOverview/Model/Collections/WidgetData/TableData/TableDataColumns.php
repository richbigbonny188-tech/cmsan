<?php
/*--------------------------------------------------------------
   TableDataColumns.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn;
use InvalidArgumentException;
use IteratorAggregate;
use Webmozart\Assert\Assert;

/**
 * Class representing a collection of table columns.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData
 */
class TableDataColumns implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var TableDataColumn[]
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
     * @param TableDataColumn ...$columns Values.
     *
     * @return TableDataColumns Instance.
     */
    public static function create(TableDataColumn ...$columns): self
    {
        return new self($columns);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
    
    
    /**
     * Return column by field.
     *
     * @param string $field Table column's field name.
     *
     * @return TableDataColumn Table column with provided field name.
     */
    public function getByField(string $field): TableDataColumn
    {
        Assert::stringNotEmpty($field);
        
        /**
         * @var TableDataColumn $column
         */
        foreach ($this->values as $column) {
            if ($field === $column->field()) {
                return $column;
            }
        }
        
        throw new InvalidArgumentException("Column not found");
    }
}