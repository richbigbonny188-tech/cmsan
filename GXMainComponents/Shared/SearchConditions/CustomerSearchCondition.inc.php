<?php
/* --------------------------------------------------------------
   CustomerSearchCondition.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerSearchCondition
 */
class CustomerSearchCondition extends GeneralSearchCondition
{
    /**
     * Return the allowed columns as an array.
     *
     * @return array
     */
    protected static function allowedColumns()
    {
        return [
            'customers_id',
            'customers_cid',
            'customers_vat_id',
            'customers_vat_id_status',
            'customers_warning',
            'customers_status',
            'customers_gender',
            'customers_firstname',
            'customers_lastname',
            'customers_dob',
            'customers_email_address',
            'customers_default_address_id',
            'customers_telephone',
            'customers_fax',
            'customers_password',
            'customers_newsletter',
            'customers_newsletter_mode',
            'member_flag',
            'delete_user',
            'account_type',
            'password_request_key',
            'payment_unallowed',
            'shipping_unallowed',
            'refferers_id',
            'customers_date_added',
            'customers_last_modified',
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
            'customers',
        ];
    }
}