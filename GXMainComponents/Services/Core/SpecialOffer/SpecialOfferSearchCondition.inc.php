<?php
/* --------------------------------------------------------------
   SpecialOfferSearchCondition.inc.php 2018-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SpecialOfferSearchCondition extends GeneralSearchCondition
{
    /**
     * Return the allowed columns as an array.
     *
     * @return array
     */
    protected static function allowedColumns()
    {
        return [
            'specials_id',
            'products_id',
            'specials_quantity',
            'specials_new_products_price',
            'specials_date_added',
            'specials_last_modified',
            'begins_date',
            'expires_date',
            'date_status_change',
            'status'
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
            'specials',
        ];
    }
}
