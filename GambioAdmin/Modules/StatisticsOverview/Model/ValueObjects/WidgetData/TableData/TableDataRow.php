<?php
/*--------------------------------------------------------------
   TableDataRow.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\TableData\TableDataRows\RowFields;

/**
 * Class representing a table row.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData
 */
class TableDataRow
{
    /**
     * Row fields.
     *
     * @var RowFields
     */
    private $fields;
    
    
    /**
     * Constructor.
     *
     * @param RowFields $fields Row fields.
     */
    private function __construct(RowFields $fields)
    {
        $this->fields = $fields;
    }
    
    
    /**
     * Create instance.
     *
     * @param RowFields $fields Row fields.
     *
     * @return TableDataRow Instance.
     */
    public static function create(RowFields $fields): self
    {
        return new self($fields);
    }
    
    
    /**
     * Return fields.
     *
     * @return RowFields Row fields.
     */
    public function fields(): RowFields
    {
        return $this->fields;
    }
}