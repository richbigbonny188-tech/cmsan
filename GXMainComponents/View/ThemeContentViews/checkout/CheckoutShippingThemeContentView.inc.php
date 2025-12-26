<?php
/* --------------------------------------------------------------
  CheckoutShippingThemeContentView.inc.php 2021-08-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_shipping.php,v 1.15 2003/04/08); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_shipping.php,v 1.20 2003/08/20); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_shipping.php 1037 2005-07-17 15:25:32Z gwinger $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once DIR_FS_INC . 'xtc_address_label.inc.php';
require_once DIR_FS_INC . 'xtc_count_shipping_modules.inc.php';

class CheckoutShippingThemeContentView extends ThemeContentView
{
    protected $free_shipping;
    protected $shipping_free_over;
    protected $quotes_array;
    protected $coo_xtc_price;
    protected $address_book_id;
    protected $customer_id;
    protected $language;
    protected $selected_shipping_method;
    protected $style_edit_active;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('checkout_shipping.html');
        $this->set_flat_assigns(true);
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'free_shipping',
                                                                        'shipping_free_over',
                                                                        'quotes_array',
                                                                        'coo_xtc_price',
                                                                        'address_book_id',
                                                                        'language',
                                                                        'style_edit_active'
                                                                    ]);
        if (empty($t_uninitialized_array)) {
            $this->_assignErrorMessage();
            $this->_assignUrls();
            $this->_assignAddress();
            $this->_assignParcelshopFinder();
            $this->_assignLightboxData();
            $this->_assignStyleEditFlag();
            $this->_assignShippingBlock();
        } else {
            trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    /**
     * check if country of selected shipping address is not allowed
     */
    protected function _assignErrorMessage()
    {
        $query  = "SELECT a.address_book_id
					FROM
						" . TABLE_ADDRESS_BOOK . " a,
						" . TABLE_COUNTRIES . " c
					WHERE
						a.address_book_id = '" . $this->address_book_id . "' AND
						a.entry_country_id = c.countries_id AND
						c.status = 1";
        $result = xtc_db_query($query);
        if (xtc_db_num_rows($result) == 0) {
            $this->set_content_data('error', ERROR_INVALID_SHIPPING_COUNTRY);
        }
    }
    
    
    protected function _assignAddress()
    {
        $this->set_content_data('ADDRESS_LABEL',
                                xtc_address_label($this->customer_id, $this->address_book_id, true, ' ', '<br />'));
        
        $this->_assignAmazonAddress();
    }
    
    
    /**
     * @deprecated
     */
    protected function _assignDeprecated()
    {
        // deprecated
    }
    
    
    protected function _assignParcelshopFinder()
    {
        if ((bool)gm_get_conf('MODULE_CENTER_PARCELSHOPFINDER_INSTALLED') === true) {
            $this->set_content_data('url_parcelshopfinder',
                                    xtc_href_link('shop.php', 'do=Parcelshopfinder&checkout_started=1', 'SSL'));
            
            $customerService    = StaticGXCoreLoader::getService('Customer');
            $customer           = $customerService->getCustomerById(new IdType($_SESSION['customer_id']));
            $addressBookService = StaticGXCoreLoader::getService('AddressBook');
            $addressBook        = $addressBookService->getCustomerAddresses($customer);
            $hasPSFAddress      = false;
            foreach ($addressBook as $abEntry) {
                if (in_array((string)$abEntry->getAddressClass(),
                             ['packstation', 'postfiliale', 'packstation_2', 'postfiliale_2', 'parcelshop'],
                             true)) {
                    $hasPSFAddress = true;
                }
            }
            $this->set_content_data('has_psf_address', $hasPSFAddress);
        }
    }
    
    
    protected function _assignLightboxData()
    {
        $this->set_content_data('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CHECKOUT'));
        $this->set_content_data('LIGHTBOX_CLOSE', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
    }
    
    
    protected function _assignStyleEditFlag()
    {
        if ($this->style_edit_active) {
            $this->set_content_data('STYLE_EDIT', 1);
        } else {
            $this->set_content_data('STYLE_EDIT', 0);
        }
    }
    
    
    protected function _assignAmazonAddress()
    {
        if (empty($_SESSION['amazonadvpay_order_ref_id']) !== true) {
            $this->set_content_data('amazon_checkout_address', '<div id="addressBookWidgetDiv"></div>');
        }
        
        if (!empty($_COOKIE['amazon_Login_accessToken']) && $_SESSION['payment'] === 'amazonadvpay') {
            $amzAdvancedPayment = MainFactory::create('AmazonAdvancedPayment');
            $ssoConfiguration   = MainFactory::create('SingleSignonConfigurationStorage');
            $widgetsSrc         = $amzAdvancedPayment->get_lpa_widgets_url();
            $this->set_content_data('amz_seller_id', $amzAdvancedPayment->seller_id);
            $this->set_content_data('amz_client_id', $ssoConfiguration->get('services/amazon/clientId'));
            $this->set_content_data('amz_widgets_src', $widgetsSrc);
            $this->set_content_data('amz_country_txt', $amzAdvancedPayment->get_text('country_not_allowed'));
        }
    }
    
    
    protected function _assignShippingBlock()
    {
        if (xtc_count_shipping_modules() > 1) {
            $this->set_content_data('GM_MORE_SHIPPING', 1);
        }
        
        /* @var CheckoutShippingModulesThemeContentView $shippingModulesView */
        $shippingModulesView = MainFactory::create_object('CheckoutShippingModulesThemeContentView');
        $shippingModulesView->set_('free_shipping', $this->free_shipping);
        $shippingModulesView->set_('quotes_array', $this->quotes_array);
        $shippingModulesView->set_('shipping_free_over', $this->shipping_free_over);
        $shippingModulesView->set_('coo_xtc_price', $this->coo_xtc_price);
        if ($this->selected_shipping_method !== null) {
            $shippingModulesView->set_('selected_shipping_method', $this->selected_shipping_method);
        }
        $html = $shippingModulesView->get_html();
        $this->set_content_data('SHIPPING_BLOCK', $html);
        
        if (sizeof($shippingModulesView->get_('quotes_array')) == 1 || $this->free_shipping) {
            $this->set_content_data('GM_FREE_SHIPPING_ACTIVATED', 1);
        }
    }
    
    protected function _assignUrls()
    {
        $this->set_content_data('FORM_ACTION_URL', xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
    
    
    /**
     * @param int $p_addressBookId
     */
    public function set_address_book_id($p_addressBookId)
    {
        if ($p_addressBookId === false) {
            $this->address_book_id = false;
        } elseif (check_data_type($p_addressBookId, 'int')) {
            $this->address_book_id = (int)$p_addressBookId;
        }
    }
    
    
    /**
     * @return int
     */
    public function get_address_book_id()
    {
        return $this->address_book_id;
    }
    
    
    /**
     * @param xtcPrice $xtcPrice
     */
    public function set_coo_xtc_price(xtcPrice $xtcPrice)
    {
        $this->coo_xtc_price = $xtcPrice;
    }
    
    
    /**
     * @return xtcPrice
     */
    public function get_coo_xtc_price()
    {
        return $this->coo_xtc_price;
    }
    
    
    /**
     * @param int $p_customerId
     */
    public function set_customer_id($p_customerId)
    {
        $this->customer_id = (int)$p_customerId;
    }
    
    
    /**
     * @return int
     */
    public function get_customer_id()
    {
        return $this->customer_id;
    }
    
    
    /**
     * @param bool $p_isFreeShipping
     */
    public function set_free_shipping($p_isFreeShipping)
    {
        $this->free_shipping = $p_isFreeShipping;
    }
    
    
    /**
     * @return bool
     */
    public function get_free_shipping()
    {
        return $this->free_shipping;
    }
    
    
    /**
     * @param string $p_language
     */
    public function set_language($p_language)
    {
        $this->language = basename((string)$p_language);
    }
    
    
    /**
     * @return string
     */
    public function get_language()
    {
        return $this->language;
    }
    
    
    /**
     * @param array $quotesArray
     */
    public function set_quotes_array(array $quotesArray)
    {
        $this->quotes_array = $quotesArray;
    }
    
    
    /**
     * @return array
     */
    public function get_quotes_array()
    {
        return $this->quotes_array;
    }
    
    
    /**
     * @param string $p_shippingMethod
     */
    public function set_selected_shipping_method($p_shippingMethod)
    {
        $this->selected_shipping_method = (string)$p_shippingMethod;
    }
    
    
    /**
     * @return string
     */
    public function get_selected_shipping_method()
    {
        return $this->selected_shipping_method;
    }
    
    
    /**
     * @param double $p_shippingFreeOver
     */
    public function set_shipping_free_over($p_shippingFreeOver)
    {
        $this->shipping_free_over = (double)$p_shippingFreeOver;
    }
    
    
    /**
     * @return double
     */
    public function get_shipping_free_over()
    {
        return $this->shipping_free_over;
    }
    
    
    /**
     * @param bool $p_isStyleEditActive
     */
    public function set_style_edit_active($p_isStyleEditActive)
    {
        $this->style_edit_active = (bool)$p_isStyleEditActive;
    }
    
    
    /**
     * @return bool
     */
    public function get_style_edit_active()
    {
        return $this->style_edit_active;
    }
}
