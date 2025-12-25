<?php
/*--------------------------------------------------------------
   TableDataColumn.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData;

/**
 * Interface representing a table column.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData
 */
interface TableDataColumn
{
    /**
     * Return title.
     *
     * @return string Title.
     */
    public function title(): string;
    
    
    /**
     * Return field.
     *
     * @return string Field.
     */
    public function field(): string;
    
    
    /**
     * Return type.
     *
     * @return string Type.
     */
    public function type(): string;
}