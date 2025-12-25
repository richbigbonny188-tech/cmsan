<?php

/* --------------------------------------------------------------
   QuickEditOverviewColumnsInterface.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditOverviewColumnsInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
interface QuickEditOverviewColumnsInterface
{
    /**
     * Get the DataTableColumnCollection of the table.
     *
     * @return DataTableColumnCollection Returns the DataTableColumnCollection of the table.
     */
    public function getColumns();
    
    
    /**
     * Serializes the data of a table column.
     *
     * @return array Returns the serialized table column data.
     */
    public function serializeColumns();
}