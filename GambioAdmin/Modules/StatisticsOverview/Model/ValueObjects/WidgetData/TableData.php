<?php
/*--------------------------------------------------------------
   TableData.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataColumns;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataRows;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

/**
 * Class representing table data.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData
 */
class TableData implements WidgetData
{
    /**
     * Type name.
     */
    private const TYPE = "table";
    
    /**
     * Rows.
     *
     * @var TableDataRows
     */
    private $rows;
    
    /**
     * Columns.
     *
     * @var TableDataColumns
     */
    private $columns;
    
    /**
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param TableDataColumns $columns Columns.
     * @param TableDataRows    $rows    Rows.
     */
    private function __construct(TableDataColumns $columns, TableDataRows $rows)
    {
        $this->rows    = $rows;
        $this->columns = $columns;
        $this->type    = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param TableDataColumns $columns Columns.
     * @param TableDataRows    $rows    Rows.
     *
     * @return TableData Instance.
     */
    public static function create(TableDataColumns $columns, TableDataRows $rows): self
    {
        return new self($columns, $rows);
    }
    
    
    /**
     * Return rows.
     *
     * @return TableDataRows Rows.
     */
    public function rows(): TableDataRows
    {
        return $this->rows;
    }
    
    
    /**
     * Return columns.
     *
     * @return TableDataColumns Columns.
     */
    public function columns(): TableDataColumns
    {
        return $this->columns;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
}