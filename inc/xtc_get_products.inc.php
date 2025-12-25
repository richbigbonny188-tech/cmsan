<?php
/* --------------------------------------------------------------
   xtc_get_products.inc.php 2021-07-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -----------------------------------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003  nextcommerce (xtc_address_format.inc.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2005  XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_products.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------
*/

require(DIR_FS_CATALOG.'includes/classes/xtcPrice.php');
  
  function xtc_get_products($session) {
  
    if (!is_array($session) || !is_array($session['cart']->contents))
      {
          return false;
      }
  
        $products_array = array();
        reset($session);
        foreach($session['cart']->contents as $products_id => $value) {
          $products_query = xtc_db_query("select p.products_id, pd.products_name,p.products_image, p.products_model, p.products_price, p.products_discount_allowed, p.products_weight, p.products_tax_class_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id='" . xtc_get_prid($products_id) . "' and pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
          if ($products = xtc_db_fetch_array($products_query)) {
            $prid = $products['products_id'];
  
  
            // dirty workaround
            $xtPrice = new xtcPrice($session['currency'],$session['customers_status']['customers_status_id']);
            $products_price=$xtPrice->xtcGetPrice($products['products_id'],
                                          $format=false,
                                          $session['cart']->contents[$products_id]['qty'],
                                          $products['products_tax_class_id'],
                                          $products['products_price']);
  
  
            $products_array[] = array('id' => $products_id,
                                      'name' => $products['products_name'],
                                      'model' => $products['products_model'],
                                      'image' => $products['products_image'],
                                      'price' => $products_price+attributes_price($products_id,$session),
                                      'quantity' => $session['cart']->contents[$products_id]['qty'],
                                      'weight' => $products['products_weight'],
                                      'final_price' => ($products_price+attributes_price($products_id,$session)),
                                      'tax_class_id' => $products['products_tax_class_id'],
                                      'attributes' => $session['contents'][$products_id]['attributes']);
          }
        }
  
        return $products_array;
      }
  
  function attributes_price($products_id,$session) {
        $xtPrice = new xtcPrice($session['currency'],$session['customers_status']['customers_status_id']);
        if (isset($session['contents'][$products_id]['attributes'])) {
          reset($session['contents'][$products_id]['attributes']);
          foreach($session['contents'][$products_id]['attributes'] as $option => $value) {
            $attribute_price_query = xtc_db_query("select pd.products_tax_class_id, p.options_values_price, p.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . (int)$products_id . "' and p.options_id = '" . (int)$option . "' and pd.products_id = p.products_id and p.options_values_id = '" . (int)$value . "'");
            $attribute_price = xtc_db_fetch_array($attribute_price_query);
            if ($attribute_price['price_prefix'] == '+') {
              $attributes_price += $xtPrice->xtcFormat($attribute_price['options_values_price'],false,$attribute_price['products_tax_class_id']);
            } else {
              $attributes_price -= $xtPrice->xtcFormat($attribute_price['options_values_price'],false,$attribute_price['products_tax_class_id']);
            }
          }
        }
        return $attributes_price;
      }