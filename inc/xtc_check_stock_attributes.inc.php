<?php
/* --------------------------------------------------------------
   xtc_check_stock_attributes.inc.php 2022-11-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_check_stock_attributes.inc.php); www.nextcommerce.org 
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_check_stock_attributes.inc.php 899 2022-05-03 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function xtc_check_stock_attributes($attribute_id, $products_quantity)
{
    
    $stock_query = xtc_db_query("SELECT
                                  attributes_stock
                                  FROM " . TABLE_PRODUCTS_ATTRIBUTES . "

                                  WHERE products_attributes_id='" . (int)$attribute_id . "' LIMIT 1");
    
    $stock_data = xtc_db_fetch_array($stock_query);
    
    $download_query = xtc_db_query("SELECT

                                   products_attributes_id

                                  FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "

                                  WHERE products_attributes_id='" . (int)$attribute_id . "' LIMIT 1");
    
    $stock_left = $products_quantity;
    if ($stock_data !== null
        && (!xtc_db_num_rows($download_query)
            || (defined('DOWNLOAD_STOCK_CHECK')
                && DOWNLOAD_STOCK_CHECK === 'true'))) {
        $stock_left = $stock_data['attributes_stock'] - $products_quantity;
    }
    
    $out_of_stock = '';
    
    if ($stock_left < 0) {
        $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }
    
    return $out_of_stock;
}
  