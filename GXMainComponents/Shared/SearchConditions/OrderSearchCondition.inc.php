<?php
/* --------------------------------------------------------------
   OrderSearchCondition.inc.php 2023-07-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderSearchCondition
 */
class OrderSearchCondition extends GeneralSearchCondition
{
    /**
     * Return the allowed columns as an array.
     *
     * @return array
     */
    protected static function allowedColumns()
    {
        return [
            # orders
            'orders_id',
            'customers_id',
            'customers_cid',
            'customers_vat_id',
            'customers_status',
            'customers_status_name',
            'customers_status_image',
            'customers_status_discount',
            'customers_name',
            'customers_firstname',
            'customers_lastname',
            'customers_gender',
            'customers_company',
            'customers_street_address',
            'customers_house_number',
            'customers_additional_info',
            'customers_suburb',
            'customers_city',
            'customers_postcode',
            'customers_state',
            'customers_country',
            'customers_telephone',
            'customers_email_address',
            'customers_address_format_id',
            'delivery_name',
            'delivery_firstname',
            'delivery_lastname',
            'delivery_gender',
            'delivery_company',
            'delivery_street_address',
            'delivery_house_number',
            'delivery_additional_info',
            'delivery_suburb',
            'delivery_city',
            'delivery_postcode',
            'delivery_state',
            'delivery_country',
            'delivery_country_iso_code_2',
            'delivery_address_format_id',
            'billing_name',
            'billing_firstname',
            'billing_lastname',
            'billing_gender',
            'billing_company',
            'billing_street_address',
            'billing_house_number',
            'billing_additional_info',
            'billing_suburb',
            'billing_city',
            'billing_postcode',
            'billing_state',
            'billing_country',
            'billing_country_iso_code_2',
            'billing_address_format_id',
            'payment_method',
            'cc_type',
            'cc_owner',
            'cc_number',
            'cc_expires',
            'cc_start',
            'cc_issue',
            'cc_cvv',
            'comments',
            'last_modified',
            'date_purchased',
            'orders_status',
            'orders_date_finished',
            'currency',
            'currency_value',
            'account_type',
            'payment_class',
            'shipping_method',
            'shipping_class',
            'order_total_weight',
            'customers_ip',
            'language',
            'afterbuy_success',
            'afterbuy_id',
            'refferers_id',
            'conversion_type',
            'orders_ident_key',
            'gm_order_send_date',
            'gm_send_order_status',
            'gm_cancel_date',
            'abandonment_download',
            'abandonment_service',
            'orders_hash',
            'intraship_shipmentnumber',
            'exported',
            'gambio_hub_module',
            'gambio_hub_module_title',
            'gambio_hub_transaction_code',
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
            'orders',
        ];
    }
}