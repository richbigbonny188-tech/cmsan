<?php
/* --------------------------------------------------------------
   WishListThemeContentView.inc.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(best_sellers.php,v 1.20 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (best_sellers.php,v 1.10 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: best_sellers.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

MainFactory::load_class('WishListContentViewInterface');

/**
 * Class WishListThemeContentView
 */
class WishListThemeContentView extends ThemeContentView implements WishListContentViewInterface
{
    protected $products = [];
    protected $hiddenOptions;
    
    /** @var wishList_ORIGIN $coo_whishlist */
    protected $coo_whishlist;
    protected $infoMessage                          = null;
    protected $gmHistory                            = [];
    protected $anyOutOfStock;
    protected $allowCheckout;
    protected $orderDetailsWishListThemeContentView = null;
    protected $listCountDisabledProducts = 0;
    protected $disabledProductNames;
    protected $listIsEmpty = false;
    /**
     * @var array
     */
    protected $productErrorMessages;
    protected $itemsRemovedFromList;

    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('wish_list.html');
        $this->set_flat_assigns(true);
    }
    
    
    public function prepare_data()
    {
        $this->_assignFilledCart();
        
        if ($this->coo_whishlist->count_contents() <= 0) {
            $this->_assignEmptyCart();
        }
        
        if ($this->itemsRemovedFromList >= 1) {
    
            $this->_assignContentRemovedProductsMessage();
        }
        
        $this->_setContentDataListDisabledProduct();
    }
    
    
    protected function _assignFilledCart()
    {
        $this->anyOutOfStock = 0;
        
        $this->products = $this->coo_whishlist->get_products();
        $this->_buildAttributesArray();
        
        $this->set_content_data('FORM_ACTION_URL',
                                xtc_href_link('wish_list.php', 'action=update_product', 'NONSSL', true, true, true));
        $this->set_content_data('HIDDEN_OPTIONS', $this->hiddenOptions);
        $this->_orderDetails();
        
        if ($this->infoMessage !== null) {
            $this->set_content_data('info_message', str_replace('+', ' ', htmlentities_wrapper($this->infoMessage)));
        }
    }
    
    
    protected function _assignEmptyCart()
    {
        $cart_empty = true;
        if ($this->infoMessage !== null) {
            $this->set_content_data('info_message', str_replace('+', ' ', htmlentities_wrapper($this->infoMessage)));
        }
        $this->set_content_data('cart_empty', $this->cartIsEmpty = $cart_empty);
    }
    
    protected function _assignContentRemovedProductsMessage(): void
    {
        /** @var LanguageTextManager $textManager */
        $textManager = MainFactory::create('LanguageTextManager', 'wish_list', $_SESSION['languages_id']);
        $message     = $textManager->get_text('text_items_removed');
        
        $this->set_content_data('text_removed_products', $message);
    }
    
    protected function _buildAttributesArray()
    {
        $hiddenOptionsArray = [];
        
        for ($i = 0; $i < count($this->products); $i++) {
            $isValidProduct = true;
            
            // Push all attributes information in an array
            if (isset($this->products[$i]['attributes'])) {
                foreach ($this->products[$i]['attributes'] as $option => $value) {
                    $hiddenOptionsArray[$i][] = [
                        'name'  => 'id[' . $this->products[$i]['id'] . '][' . $option . ']',
                        'value' => $value,
                    ];
                    
                    if ((int)$value > 0) {
                        $query = $this->getQueryForAttributes($this->products[$i], $option, $value);
                        
                        $attributes       = xtc_db_query($query);
                        $attributesValues = xtc_db_fetch_array($attributes);
                        
                        if (empty($attributesValues)) {
                            $isValidProduct = false;
                        } else {
                            $this->products[$i][$option]['products_options_name']        = $attributesValues['products_options_name'];
                            $this->products[$i][$option]['options_values_id']            = $value;
                            $this->products[$i][$option]['products_options_values_name'] = $attributesValues['products_options_values_name'];
                            $this->products[$i][$option]['options_values_price']         = $attributesValues['options_values_price'];
                            $this->products[$i][$option]['price_prefix']                 = $attributesValues['price_prefix'];
                            $this->products[$i][$option]['weight_prefix']                = $attributesValues['weight_prefix'];
                            $this->products[$i][$option]['options_values_weight']        = $attributesValues['options_values_weight'];
                            $this->products[$i][$option]['attributes_stock']             = $attributesValues['attributes_stock'];
                            $this->products[$i][$option]['products_attributes_id']       = $attributesValues['products_attributes_id'];
                            $this->products[$i][$option]['products_attributes_model']    = $attributesValues['attributes_model'];
                        }
                    }
                }
            }
            
            if (!$isValidProduct) {
                $query = "DELETE FROM customers_wishlist WHERE products_id = '"
                         . xtc_db_input($this->products[$i]['id']) . "'";
                xtc_db_query($query);
                
                $query = "DELETE FROM customers_wishlist_attributes WHERE products_id = '"
                         . xtc_db_input($this->products[$i]['id']) . "'";
                xtc_db_query($query);
                
                unset($_SESSION['wishList']->contents[$this->products[$i]['id']]);
                unset($this->products[$i]);
                unset($hiddenOptionsArray[$i]);
            }
        }
        
        $this->hiddenOptions = $hiddenOptionsArray;
    }
    
    
    /**
     * @param array $product
     * @param       $option
     * @param       $value
     *
     * @return string
     */
    protected function getQueryForAttributes(array $product, $option, $value)
    {
        $query = "SELECT `popt`.`products_options_name`, `poval`.`products_options_values_name`, `pa`.`options_values_price`, `pa`.`price_prefix`, `pa`.`weight_prefix`, `pa`.`options_values_weight`, `pa`.`attributes_stock`,`pa`.`products_attributes_id`,`pa`.`attributes_model`
													  FROM `" . TABLE_PRODUCTS_OPTIONS . "` popt, `"
                 . TABLE_PRODUCTS_OPTIONS_VALUES . "` poval, `" . TABLE_PRODUCTS_ATTRIBUTES . "` pa
													  WHERE pa.products_id = '" . (int)$product['id'] . "'
													   AND pa.options_id = '" . (int)$option . "'
													   AND pa.options_id = popt.products_options_id
													   AND pa.options_values_id = '" . (int)$value . "'
													   AND pa.options_values_id = poval.products_options_values_id
													   AND popt.language_id = '" . (int)($_SESSION['languages_id'] ?? null) . "'
													   AND poval.language_id = '" . (int)($_SESSION['languages_id'] ?? null)
                 . "'";
        
        return $query;
    }
    
    
    protected function _orderDetails()
    {
        # order details
        /** @var OrderDetailsWishListThemeContentView $orderDetailsWishListThemeContentView */
        $this->orderDetailsWishListThemeContentView = MainFactory::create_object('OrderDetailsWishListThemeContentView');
        
        $this->orderDetailsWishListThemeContentView->setProductsArray($this->products);
        $t_view_html = $this->orderDetailsWishListThemeContentView->prepare_data();
        
        $this->set_content_data('MODULE_order_details', $t_view_html);
        
        if (STOCK_CHECK == 'true') {
            if ($this->anyOutOfStock == 1) {
                if (STOCK_ALLOW_CHECKOUT == 'true') {
                    // write permission in session
                    $this->allowCheckout = 'true';
                    $this->set_content_data('info_message',
                                            sprintf(OUT_OF_STOCK_CAN_CHECKOUT, STOCK_MARK_PRODUCT_OUT_OF_STOCK));
                } else {
                    $this->allowCheckout = 'false';
                    $this->set_content_data('info_message',
                                            sprintf(OUT_OF_STOCK_CANT_CHECKOUT, STOCK_MARK_PRODUCT_OUT_OF_STOCK));
                }
            } else {
                $this->allowCheckout = 'true';
            }
        }
    }
    
    
    /**
     * @param int $itemsRemovedFromList
     */
    public function setItemsRemovedFromList(int $itemsRemovedFromList): void
    {
        $this->itemsRemovedFromList = $itemsRemovedFromList;
    }
    
    
    /**
     * @deprecated
     */
    protected function _bofGmModGXCustomizer()
    {
        // deprecated
    }
    
    
    /**
     * @return wishList_ORIGIN
     */
    public function getCooWhishlist()
    {
        return $this->coo_whishlist;
    }
    
    
    /**
     * @param wishList_ORIGIN $coo_whishlist
     */
    public function setCooWhishlist(wishList_ORIGIN $coo_whishlist)
    {
        $this->coo_whishlist = $coo_whishlist;
    }
    
    /**
     * @param int $countDisabled
     */
    public function setListCountDisabledProducts(int $countDisabled): void
    {
        $this->listCountDisabledProducts = $countDisabled;
    }
    
    
    /**
     * @param string|null $names
     */
    public function setDisabledProductNames(?string $names): void
    {
        $this->disabledProductNames = $names;
    }
    
    /**
     * @return null
     */
    public function getInfoMessage()
    {
        return $this->infoMessage;
    }
    
    
    /**
     * @param $infoMessage
     */
    public function setInfoMessage($infoMessage)
    {
        $this->infoMessage = $infoMessage;
    }
    
    
    /**
     * @return mixed
     */
    public function getGmHistory()
    {
        return $this->gmHistory;
    }
    
    
    /**
     * @param array $gmHistory
     */
    public function setGmHistory(array $gmHistory)
    {
        $this->gmHistory = $gmHistory;
    }
    
    
    /**
     * @return mixed
     */
    public function getAnyOutOfStock()
    {
        return $this->anyOutOfStock;
    }
    
    
    /**
     * @param mixed $anyOutOfStock
     */
    public function setAnyOutOfStock(&$anyOutOfStock)
    {
        $this->anyOutOfStock = &$anyOutOfStock;
    }
    
    
    /**
     * @return mixed
     */
    public function getHiddenOptions()
    {
        return $this->hiddenOptions;
    }
    
    
    /**
     * @param mixed $hiddenOptions
     */
    public function setHiddenOptions($hiddenOptions)
    {
        $this->hiddenOptions = $hiddenOptions;
    }
    
    
    /**
     * @return mixed
     */
    public function getAllowCheckout()
    {
        return $this->allowCheckout;
    }
    
    
    /**
     * @param mixed $allowCheckout
     */
    public function setAllowCheckout(&$allowCheckout)
    {
        $this->allowCheckout = &$allowCheckout;
    }
    
    
    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }
    
    
    /**
     * @param array $products
     */
    public function setProducts(array $products)
    {
        $this->products = $products;
    }
    
    
    /**
     * @return null|OrderDetailsWishListThemeContentView
     */
    public function getOrderDetailsWishListContentView()
    {
        return $this->orderDetailsWishListThemeContentView;
    }
    /**
     * @param array $productErrorMessages
     */
    public function setProductMessages(array $productErrorMessages)
    {
        $this->productErrorMessages = $productErrorMessages;
    }

    public function getProductMessages()
    {
        return $this->productErrorMessages;
    }
    
    
    protected function _setContentDataListDisabledProduct(): void
    {
        $containsDisabledProduct    = $this->listCountDisabledProducts !== 0;
        $removeDisabledProductsLink = xtc_href_link('wish_list.php', xtc_get_all_get_params('remove_disabled_products') . '&remove_disabled_products=1');
        $message                    = $this->disabledProductMessageFormatString($this->listCountDisabledProducts > 1);
        $message                    = sprintf($message, $this->disabledProductNames, $removeDisabledProductsLink);
        
        $this->set_content_data('list_contains_disabled', $containsDisabledProduct);
        
        if ($containsDisabledProduct) {
        
            $this->set_content_data('list_disabled_message', $message);
        }
    }
    
    /**
     * @param bool $plural is there more than one product disabled?
     *
     * @return string format string for sprintf
     */
    protected function disabledProductMessageFormatString(bool $plural): string
    {
        /** @var LanguageTextManager $textManager */
        $textManager = MainFactory::create('LanguageTextManager', 'wish_list', $_SESSION['languages_id']);
        $phraseName  = $plural ? 'text_empty_disabled_plural' : 'text_empty_disabled_singular';
        
        return $textManager->get_text($phraseName);
    }
}
