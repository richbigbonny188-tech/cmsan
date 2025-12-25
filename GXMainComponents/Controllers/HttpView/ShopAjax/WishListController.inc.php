<?php
/* --------------------------------------------------------------
   WishListController.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Shop\ProductModifiers\Helpers\ModifierTranslatorHelper;

/**
 * Class WishListController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class WishListController extends HttpViewController
{
    /**
     * @var array
     */
    protected $productErrorMessages;

    /**
     * @return HttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     */
    public function actionDefault()
    {
        $json = $this->_getWishListJson();
        
        return MainFactory::create('JsonHttpControllerResponse', $json);
    }
    
    
    /**
     * @return HttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     */
    public function actionAdd()
    {
        # fake environment
        $_POST['submit_target'] = $_POST['target'];
        
        $this->_performAction('add_product');
        
        $result = [
            'success' => true,
            'type'    => 'url',
            'url'     => 'wish_list.php',
            'content' => []
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * @return HttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     */
    public function actionDelete()
    {
        $this->_performAction('update_product');
        
        return $this->actionDefault();
    }
    
    
    /**
     * @return HttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     */
    public function actionUpdate()
    {
        $this->_performAction('update_wishlist');
        
        return $this->actionDefault();
    }
    
    
    /**
     * @return HttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     */
    public function actionAddToCart()
    {
        $this->_performAction('wishlist_to_cart');
        
        return $this->actionDefault();
    }
    
    
    /**
     * @param string $p_action
     */
    protected function _performAction($p_action)
    {
        ModifierTranslatorHelper::translateGlobals();
        $t_turbo_buy_now = true;    # flag used in cart_actions
        $t_show_cart     = false;        # will be changed in cart_actions
        $t_show_details  = false;    # will be changed in cart_actions
        
        $cartActionsProcess = MainFactory::create_object('CartActionsProcess');
        $cartActionsProcess->set_data('GET', $_GET);

        $postData = $_POST;

        if (isset($postData['modifiers']['property'])) {
            $postData['properties_values_ids'] = $postData['modifiers']['property'];
        }

        $idArray = [];

        if (isset($postData['modifiers']['attribute'])) {
            foreach ($postData['modifiers']['attribute'] as $optionId => $valueId) {
                $idArray[$optionId] = $valueId;
            }

            if(isset($postData['id'])) {
                foreach ($postData['id'] as $optionId => $valueId) {
                    $idArray[$optionId] = $valueId;
                }
            }

            $postData['id'] = $idArray;
        }

        $cartActionsProcess->set_data('POST', $postData);
        
        // Lokale
        if (isset($t_turbo_buy_now)) {
            $cartActionsProcess->reference_set_('turbo_buy_now', $t_turbo_buy_now);
        }
        if (isset($t_show_cart)) {
            $cartActionsProcess->reference_set_('show_cart', $t_show_cart);
        }
        if (isset($t_show_details)) {
            $cartActionsProcess->reference_set_('show_details', $t_show_details);
        }
        
        // Globale
        $cartActionsProcess->set_('php_self', $GLOBALS['PHP_SELF']);
        $cartActionsProcess->set_('coo_seo_boost', $GLOBALS['gmSEOBoost']);
        if (isset($GLOBALS['order']) && is_null($GLOBALS['order']) == false) {
            $cartActionsProcess->set_('coo_order', $GLOBALS['order']);
        }
        if (empty($GLOBALS['REMOTE_ADDR'])) {
            $GLOBALS['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
        }
        $cartActionsProcess->reference_set_('remote_address', $GLOBALS['REMOTE_ADDR']);
        $cartActionsProcess->set_('coo_price', $GLOBALS['xtPrice']);
        
        // Session
        if (isset($_SESSION['customer_id'])) {
            $cartActionsProcess->set_('customer_id', $_SESSION['customer_id']);
        }
        $cartActionsProcess->set_('coo_wish_list', $_SESSION['wishList']);
        $cartActionsProcess->set_('coo_cart', $_SESSION['cart']);
        if (isset($_SESSION['coo_gprint_wishlist']) && is_null($_SESSION['coo_gprint_wishlist']) == false) {
            $cartActionsProcess->set_('coo_gprint_wish_list', $_SESSION['coo_gprint_wishlist']);
        }
        if (isset($_SESSION['coo_gprint_cart']) && is_null($_SESSION['coo_gprint_cart']) == false) {
            $cartActionsProcess->set_('coo_gprint_cart', $_SESSION['coo_gprint_cart']);
        }
        if (isset($_SESSION['info_message'])) {
            $cartActionsProcess->reference_set_('info_message', $_SESSION['info_message']);
        }
        $cartActionsProcess->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
        $cartActionsProcess->set_('customers_fsk18_purchasable',
                                  isset($_SESSION['customers_status']['customers_fsk18_purchasable']) ? $_SESSION['customers_status']['customers_fsk18_purchasable'] : '0');
        $cartActionsProcess->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);
        
        $cartActionsProcess->proceed($p_action);
        
        $infoMessage = $cartActionsProcess->get_('info_message');
        if (trim($infoMessage ?? '') !== '') {
            $_SESSION['info_message'] = $infoMessage;
        }
        $this->productErrorMessages = $cartActionsProcess->get_('error_message');
    }
    
    
    /**
     * Builds a JSON array that contains the HTML snippets to build the current wish list
     *
     * @return array JSON array of the current wish list
     */
    protected function _getWishListJson()
    {
        $json = [
            'success' => true
        ];
        
        $wishListContentView = $this->_getWishListContentView();
        
        $wishListContentView->prepare_data();
        $json['products'] = $this->_getProducts($wishListContentView->getOrderDetailsWishListContentView());
        $json['content']  = $this->_getContents($wishListContentView);
        
        return $json;
    }
    
    
    /**
     * Returns an initialized WishListContentView object
     *
     * @return WishListContentView
     */
    protected function _getWishListContentView()
    {
        unset($_SESSION['any_out_of_stock']);
        
        $wishListContentView = MainFactory::create_object('WishListThemeContentView');
        
        if (isset($_SESSION['wishList']) === false) {
            trigger_error('Session has no Object wishList', E_USER_ERROR);
        }
        $wishListContentView->setCooWhishlist($_SESSION['wishList']);
        
        if (isset($_GET['info_message'])) {
            $wishListContentView->setInfoMessage($_GET['info_message']);
        }
        if (isset($_SESSION['gm_history'])) {
            $wishListContentView->setGmHistory($_SESSION['gm_history']);
        }
        if (isset($_SESSION['any_out_of_stock']) === false) {
            $_SESSION['any_out_of_stock'] = null;
        }
        $wishListContentView->setAnyOutOfStock($_SESSION['any_out_of_stock']);
        
        if (isset($_SESSION['allow_checkout']) === false) {
            $_SESSION['allow_checkout'] = null;
        }
        $wishListContentView->setAnyOutOfStock($_SESSION['allow_checkout']);
        $wishListContentView->setProductMessages($this->productErrorMessages);
        
        return $wishListContentView;
    }
    
    
    /**
     * Gets a JSON array of HTML snippets to build the product listing of the current wish list content
     *
     * @param OrderDetailsWishListContentViewInterface $orderDetailsWishListContentView
     *
     * @return array JSON array of the wish list content
     */
    protected function _getProducts(OrderDetailsWishListContentViewInterface $orderDetailsWishListContentView)
    {
        $lang         = MainFactory::create('LanguageTextManager', 'order_details', $_SESSION['languages_id']);
        $productArray = [];
        
        $orderDetailsWishListContentView->setOrderItemTemplate();
        $orderDetailsWishListContentView->set_flat_assigns(true);
        $contentArray = $orderDetailsWishListContentView->get_content_array();
        $i            = 0;
        
        $orderDetailsWishListContentView->set_content_data('is_wishlist', true);
        $orderDetailsWishListContentView->set_content_data('is_confirmation', false);
        
        foreach ($contentArray['module_content'] as $productData) {
            // $isOutOfStock = $productData['IS_OUT_OF_STOCK'];
            $imageSrc     = $productData['PRODUCTS_IMAGE']
                            && $productData['PRODUCTS_IMAGE'] !== '' ? $productData['PRODUCTS_IMAGE'] : '';
            $imageAlt     = $productData['IMAGE_ALT']
                            && $productData['IMAGE_ALT']
                               !== '' ? $productData['IMAGE_ALT'] : $productData['PRODUCTS_NAME'];
            $model        = $productData['IMAGE_ALT']
                            && $productData['IMAGE_ALT'] !== '' ? $lang->get_text('text_model') . ' '
                                                                  . $productData['PRODUCTS_MODEL'] : '';
            $weight       = $productData['GM_WEIGHT'] && $productData['GM_WEIGHT'] !== ''
                            && $productData['GM_WEIGHT'] !== '0' ? $lang->get_text('text_weight') . ' '
                                                                   . $productData['GM_WEIGHT'] . ' '
                                                                   . $lang->get_text('text_weight_unit') : '';
            $shippingTime = $productData['PRODUCTS_SHIPPING_TIME']
                            && $productData['PRODUCTS_SHIPPING_TIME'] !== '' ? $lang->get_text('text_shippingtime')
                                                                               . ' '
                                                                               . $productData['PRODUCTS_SHIPPING_TIME'] : '';
            $vpe          = !empty($productData['PRODUCTS_VPE_ARRAY']['vpe_text'])
                            && $productData['PRODUCTS_VPE_ARRAY']['vpe_text']
                               !== '' ? $productData['PRODUCTS_VPE_ARRAY']['vpe_text'] : '';
            $unit         = $productData['UNIT'] && $productData['UNIT'] !== '' ? $productData['UNIT'] : '';
            $attributes   = '';
            if ($productData['ATTRIBUTES'] && $productData['ATTRIBUTES'] !== '') {
                foreach ($productData['ATTRIBUTES'] as $attribute) {
                    $attributes .= $attribute['NAME'] . ': ' . $attribute['VALUE_NAME'] . '<br />';
                }
            }
            
            $orderDetailsWishListContentView->set_content_data('last', $i >= count($contentArray['module_content']));
            $orderDetailsWishListContentView->set_content_data('p_url', $productData['PRODUCTS_LINK']);
            $orderDetailsWishListContentView->set_content_data('p_name', $productData['PRODUCTS_NAME']);
            $orderDetailsWishListContentView->set_content_data('stock_mark', $productData['STOCK_MARK']);
            $orderDetailsWishListContentView->set_content_data('p_is_out_of_stock', $productData['IS_OUT_OF_STOCK']);
            $orderDetailsWishListContentView->set_content_data('image_src', $imageSrc);
            $orderDetailsWishListContentView->set_content_data('image_alt', $imageAlt);
            $orderDetailsWishListContentView->set_content_data('image_title', $imageAlt);
            $orderDetailsWishListContentView->set_content_data('p_model', $model);
            $orderDetailsWishListContentView->set_content_data('show_p_model', $productData['SHOW_PRODUCTS_MODEL']);
            $orderDetailsWishListContentView->set_content_data('p_weight', $weight);
            $orderDetailsWishListContentView->set_content_data('p_shipping_time', $shippingTime);
            $orderDetailsWishListContentView->set_content_data('p_attributes', $attributes);
            $orderDetailsWishListContentView->set_content_data('p_price_single', $productData['PRODUCTS_SINGLE_PRICE']);
            $orderDetailsWishListContentView->set_content_data('p_price_vpe', $vpe);
            $orderDetailsWishListContentView->set_content_data('p_shipping_info', $unit);
            $orderDetailsWishListContentView->set_content_data('p_unit', $unit);
            $orderDetailsWishListContentView->set_content_data('p_qty_name', $productData['PRODUCTS_QTY_INPUT_NAME']);
            $orderDetailsWishListContentView->set_content_data('p_qty_value', $productData['PRODUCTS_QTY_VALUE']);
            $orderDetailsWishListContentView->set_content_data('p_price_final', $productData['PRODUCTS_PRICE']);
            $orderDetailsWishListContentView->set_content_data('p_hidden_name', $productData['PRODUCTS_ID_INPUT_NAME']);
            $orderDetailsWishListContentView->set_content_data('p_hidden_value', $productData['PRODUCTS_ID_EXTENDED']);
            $orderDetailsWishListContentView->set_content_data('p_hidden_qty_name',
                                                               $productData['PRODUCTS_OLDQTY_INPUT_NAME']);
            $orderDetailsWishListContentView->set_content_data('p_hidden_qty_value',
                                                               $productData['PRODUCTS_QTY_VALUE']);
            $orderDetailsWishListContentView->set_content_data('p_hidden_cart_delete_name',
                                                               $productData['PRODUCTS_CART_DELETE_INPUT_NAME']);
            $orderDetailsWishListContentView->set_content_data('p_hidden_cart_delete_value',
                                                               $productData['PRODUCTS_ID_EXTENDED']);
            $orderDetailsWishListContentView->set_content_data('p_error_id', $productData['PRODUCTS_ID']);

            $orderDetailsWishListContentView->set_content_data('tpl_modifiers', $productData['MODIFIERS'] ?? $productData['tpl_modifiers']);
            $orderDetailsWishListContentView->set_content_data('tpl_box_delete', $productData['BOX_DELETE'] ?? null);
            $orderDetailsWishListContentView->set_content_data('p_shipping_info', $productData['TAX_SHIPPING_INFO']);
            
            $productArray['product_'
                          . $productData['PRODUCTS_ID_EXTENDED']] = $orderDetailsWishListContentView->build_html();
            $i++;
        }
        
        $orderDetailsWishListContentView->set_flat_assigns(false);
        
        return $productArray;
    }
    
    
    /**
     * Gets a JSON array of HTML snippets to build the content of the current wish list apart from its products.
     *
     * @param WishListContentViewInterface $wishListContentView
     *
     * @return array JSON array of the informational content (without products) of the wish list
     */
    protected function _getContents(WishListContentViewInterface $wishListContentView)
    {
        $contentArray            = [];
        $contentViewContentArray = $wishListContentView->get_content_array();
        $html                    = '';

        if (isset($contentViewContentArray['HIDDEN_OPTIONS']) && is_array($contentViewContentArray['HIDDEN_OPTIONS'])) {
            foreach ($contentViewContentArray['HIDDEN_OPTIONS'] as $input) {
                foreach ($input as $option) {
                    $html .= '<input type="hidden" name="' . htmlspecialchars($option['name']) . '" value="'
                             . htmlspecialchars($option['value']) . '" />';
                }
            }
        } elseif (isset($contentViewContentArray['HIDDEN_OPTIONS'])
                  && is_string($contentViewContentArray['HIDDEN_OPTIONS'])) {
            $html .= $contentViewContentArray['HIDDEN_OPTIONS'];
        }

        $html .= '<input type="hidden" name="submit_target" value="wishlist" class="force" />';

        $contentArray['hidden'] = [
            'selector' => 'hiddenOptions',
            'type'     => 'html',
            'value'    => $html
        ];

        $contentArray['errorMessageList'] = $this->_getProductErrorMessages($wishListContentView);
        return $contentArray;
    }

    /**
     * Gets the HTML for all product related messages.
     *
     * @param WishListContentViewInterface $wishListContentView
     *
     * @return array
     */
    protected function _getProductErrorMessages(WishListContentViewInterface $wishListContentView)
    {
        $messages = [];
        foreach ($wishListContentView->getProductMessages() as $productId => $message) {
            $messages[$productId] = ['selector' => 'errorMsg', 'type' => 'html', 'value' => $message];
        }

        return $messages;
    }

}
