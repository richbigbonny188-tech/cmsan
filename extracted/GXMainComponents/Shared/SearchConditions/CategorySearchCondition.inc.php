<?php
/* --------------------------------------------------------------
   CategorySearchCondition.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategorySearchCondition
 */
class CategorySearchCondition extends GeneralSearchCondition
{
    /**
     * Return the allowed columns as an array.
     *
     * @return array
     */
    protected static function allowedColumns()
    {
        return [
            # categories
            'categories_id',
            'categories_image',
            'parent_id',
            'categories_status',
            'categories_template',
            'group_permission_0',
            'group_permission_1',
            'group_permission_2',
            'group_permission_3',
            'listing_template',
            'sort_order',
            'products_sorting',
            'products_sorting2',
            'date_added',
            'last_modified',
            'categories_icon',
            'categories_icon_w',
            'categories_icon_h',
            'group_ids',
            'gm_show_attributes',
            'gm_show_graduated_prices',
            'gm_show_qty',
            'gm_priority',
            'gm_changefreq',
            'gm_sitemap_entry',
            'gm_show_qty_info',
            'show_sub_categories',
            'show_sub_categories_images',
            'show_sub_categories_names',
            'show_sub_products',
            'view_mode_tiled',
            'feature_mode',
            'feature_display_mode',
            'show_category_filter',
            
            # categories_description
            'categories_id',
            'language_id',
            'categories_name',
            'categories_heading_title',
            'categories_description',
            'categories_meta_title',
            'categories_meta_description',
            'categories_meta_keywords',
            'gm_alt_text',
            'gm_url_keywords',
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
            'categories',
            'categories_description',
        ];
    }
}