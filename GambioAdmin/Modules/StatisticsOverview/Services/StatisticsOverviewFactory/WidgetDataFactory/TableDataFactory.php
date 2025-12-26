<?php
/*--------------------------------------------------------------
   TableDataFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataColumns;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataRows;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataRows\RowFields;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn\BooleanColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn\DateColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn\DecimalColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn\IntegerColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn\PercentageColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn\TextColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField\BooleanRowField;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField\NumberRowField;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField\TextRowField;

/**
 * Class representing a table data factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory
 */
class TableDataFactory
{
    /**
     * Return table data.
     *
     * @param TableDataColumns $columns Columns.
     * @param TableDataRows    $rows    Rows.
     *
     * @return TableData Table data.
     */
    public function createTableData(TableDataColumns $columns, TableDataRows $rows): TableData
    {
        return TableData::create($columns, $rows);
    }
    
    
    /**
     * Return columns.
     *
     * @param TableDataColumn ...$columns Columns.
     *
     * @return TableDataColumns Columns.
     */
    public function createColumns(TableDataColumn ...$columns): TableDataColumns
    {
        return TableDataColumns::create(...$columns);
    }
    
    
    /**
     * Return date column.
     *
     * @param string $title            Title.
     * @param string $field            Field name.
     * @param string $inputDateFormat  Input date format.
     * @param string $outputDateFormat Output date format.
     *
     * @return DateColumn Date column.
     */
    public function createDateColumn(
        string $title,
        string $field,
        string $inputDateFormat,
        string $outputDateFormat
    ): DateColumn {
        return DateColumn::create($title, $field, $inputDateFormat, $outputDateFormat);
    }
    
    
    /**
     * Return boolean column.
     *
     * @param string $title Title.
     * @param string $field Field name.
     *
     * @return BooleanColumn Boolean column.
     */
    public function createBooleanColumn(string $title, string $field): BooleanColumn
    {
        return BooleanColumn::create($title, $field);
    }
    
    
    /**
     * Return decimal column.
     *
     * @param string $title Title.
     * @param string $field Field name.
     *
     * @return DecimalColumn Decimal column.
     */
    public function createDecimalColumn(string $title, string $field): DecimalColumn
    {
        return DecimalColumn::create($title, $field);
    }
    
    
    /**
     * Return percentage column.
     *
     * @param string $title Title.
     * @param string $field Field name.
     *
     * @return PercentageColumn Percentage column.
     */
    public function createPercentageColumn(string $title, string $field): PercentageColumn
    {
        return PercentageColumn::create($title, $field);
    }
    
    
    /**
     * Return text column.
     *
     * @param string $title Title.
     * @param string $field Field name.
     *
     * @return TextColumn Text column.
     */
    public function createTextColumn(string $title, string $field): TextColumn
    {
        return TextColumn::create($title, $field);
    }
    
    
    /**
     * Return integer column.
     *
     * @param string $title Title.
     * @param string $field Field name.
     *
     * @return IntegerColumn Integer column.
     */
    public function createIntegerColumn(string $title, string $field): IntegerColumn
    {
        return IntegerColumn::create($title, $field);
    }
    
    
    /**
     * Return rows.
     *
     * @param TableDataRow ...$rows Rows.
     *
     * @return TableDataRows Rows.
     */
    public function createRows(TableDataRow ...$rows): TableDataRows
    {
        return TableDataRows::create(...$rows);
    }
    
    
    /**
     * Return row.
     *
     * @param RowFields $fields Row fields.
     *
     * @return TableDataRow Row.
     */
    public function createRow(RowFields $fields): TableDataRow
    {
        return TableDataRow::create($fields);
    }
    
    
    /**
     * Return row fields.
     *
     * @param RowField ...$fields Row fields.
     *
     * @return RowFields Row fields.
     */
    public function createRowFields(RowField ...$fields): RowFields
    {
        return RowFields::create(...$fields);
    }
    
    
    /**
     * Return text row field.
     *
     * @param string $name  Name.
     * @param string $value Value.
     *
     * @return TextRowField Text row field.
     */
    public function createRowTextField(string $name, string $value): TextRowField
    {
        return TextRowField::create($name, $value);
    }
    
    
    /**
     * Return number row field.
     *
     * @param string $name  Name.
     * @param float  $value Value.
     *
     * @return NumberRowField Number row field.
     */
    public function createRowNumberField(string $name, float $value): NumberRowField
    {
        return NumberRowField::create($name, $value);
    }
    
    
    /**
     * Return boolean row field.
     *
     * @param string $name  Name.
     * @param bool   $value Value.
     *
     * @return BooleanRowField Boolean row field.
     */
    public function createRowBooleanField(string $name, bool $value): BooleanRowField
    {
        return BooleanRowField::create($name, $value);
    }
}