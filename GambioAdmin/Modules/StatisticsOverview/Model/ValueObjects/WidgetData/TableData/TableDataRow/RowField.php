<?php
/*--------------------------------------------------------------
   RowField.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow;

/**
 * Interface representing a table row field.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow
 */
interface RowField
{
    /**
     * Return name.
     *
     * @return string Name.
     */
    public function name(): string;
    
    
    /**
     * Return value.
     */
    public function value();
}