<?php
/* --------------------------------------------------------------
   ProductSearchCondition.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductSearchCondition
 */
class ProductSearchCondition extends GeneralSearchCondition
{
    /**
     * Return the allowed columns as an array.
     *
     * @return array
     */
    protected static function allowedColumns()
    {
        return [
            # products
            'products_id',
            'products_ean',
            'products_quantity',
            'products_shippingtime',
            'products_model',
            'group_permission_0',
            'group_permission_1',
            'group_permission_2',
            'group_permission_3',
            'products_sort',
            'products_image',
            'products_price',
            'products_discount_allowed',
            'products_date_added',
            'products_last_modified',
            'products_date_available',
            'products_weight',
            'products_status',
            'products_tax_class_id',
            'product_template',
            'options_template',
            'manufacturers_id',
            'products_ordered',
            'products_fsk18',
            'products_vpe',
            'products_vpe_status',
            'products_vpe_value',
            'products_startpage',
            'products_startpage_sort',
            'group_ids',
            'nc_ultra_shipping_costs',
            'gm_show_date_added',
            'gm_show_price_offer',
            'gm_show_weight',
            'gm_price_status',
            'gm_min_order',
            'gm_graduated_qty',
            'gm_options_template',
            'gm_priority',
            'gm_changefreq',
            'gm_show_qty_info',
            'gm_sitemap_entry',
            'products_image_w',
            'products_image_h',
            'gm_show_image',
            'properties_dropdown_mode',
            'properties_show_price',
            'use_properties_combis_weight',
            'use_properties_combis_quantity',
            'use_properties_combis_shipping_time',
            'product_type',
            
            # products_description
            'products_id',
            'language_id',
            'products_name',
            'products_description',
            'products_short_description',
            'products_keywords',
            'products_meta_title',
            'products_meta_description',
            'products_meta_keywords',
            'products_url',
            'products_viewed',
            'gm_alt_text',
            'gm_url_keywords',
            'checkout_information',
            
            # products_quantity_unit
            'products_id',
            'quantity_unit_id',
            
            # products_to_categories
            'products_id',
            'categories_id',
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
            'products',
            'products_description',
            'products_quantity_unit',
            'products_to_categories',
        ];
    }
}