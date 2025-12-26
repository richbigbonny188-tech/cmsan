<?php
/* --------------------------------------------------------------
   ManufacturerSearchCondition.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerSearchCondition
 */
class ManufacturerSearchCondition extends GeneralSearchCondition
{
    /**
     * Return the allowed columns as an array.
     *
     * @return array
     */
    protected static function allowedColumns()
    {
        return [
            # manufacturers
            'manufacturers_id',
            'manufacturers_name',
            'manufacturers_image',
            'date_added',
            'last_modified',
        ];
    }
    
    
    /**
     * Return the allowed tables as an array.
     *
     * @return array
     */
    protected static function allowedTables()
    {
        return [
            'manufacturers',
        ];
    }
}