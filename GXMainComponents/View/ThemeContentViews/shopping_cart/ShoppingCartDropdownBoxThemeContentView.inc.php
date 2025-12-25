<?php
/*--------------------------------------------------------------------
 ShoppingCartDropdownBoxThemeContentView.inc.php 2020-2-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

use Gambio\Core\Application\Application;
use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\SellingUnit\Unit\Factories\Interfaces\SellingUnitIdFactoryInterface;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;
use Gambio\Shop\SellingUnit\Unit\Services\Interfaces\SellingUnitReadServiceInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

require_once(DIR_FS_CATALOG . 'inc/xtc_get_countries.inc.php');
require_once(DIR_FS_INC . 'get_products_vpe_array.inc.php');

/**
 * Class ShoppingCartDropdownBoxThemeContentView
 */
class ShoppingCartDropdownBoxThemeContentView extends ThemeContentView
{
    protected $coo_cart;
    protected $language_id;
    protected $language_code;
    protected $customers_status_ot_discount_flag;
    protected $customers_status_ot_discount;
    protected $customers_status_show_price_tax;
    protected $customers_status_add_tax_ot;
    protected $customers_status_show_price;
    protected $customers_status_payment_unallowed;
    
    /**
     * @var SellingUnitInterface[]
     */
    protected $sellingUnits = [];
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('layout_header_cart_dropdown.html');
        $this->set_caching_enabled(false);
        $this->set_flat_assigns(true);
    }
    
    
    protected function set_validation_rules()
    {
        // SET VALIDATION RULES
        $this->validation_rules_array['coo_cart']                           = [
            'type'        => 'object',
            'object_type' => 'shoppingCart'
        ];
        $this->validation_rules_array['language_id']                        = ['type' => 'int'];
        $this->validation_rules_array['language_code']                      = ['type' => 'string'];
        $this->validation_rules_array['customers_status_ot_discount_flag']  = ['type' => 'int'];
        $this->validation_rules_array['customers_status_ot_discount']       = ['type' => 'double'];
        $this->validation_rules_array['customers_status_show_price_tax']    = ['type' => 'int'];
        $this->validation_rules_array['customers_status_add_tax_ot']        = ['type' => 'int'];
        $this->validation_rules_array['customers_status_show_price']        = ['type' => 'int'];
        $this->validation_rules_array['customers_status_payment_unallowed'] = ['type' => 'string'];
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables(['coo_cart', 'language_id', 'language_code']);
        if (empty($t_uninitialized_array)) {
            $this->content_array['products'] = [];
            $this->content_array['empty']    = 'true';
            $this->add_total();
            if ($this->coo_cart->count_contents() > 0) {
                $this->add_data();
                $this->content_array['empty']         = 'false';
                $this->content_array['productsCount'] = $this->coo_cart->count_products();
            }
            $this->content_array['showProductsCount'] = $this->show_products_count();
        } else {
            trigger_error("Variable(s) " . implode(', ',
                                                   $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    protected function add_data()
    {
        // check if data is already added
        if (!isset($this->content_array['SHIPPING_INFO'])) {
            $this->add_products();
            $this->add_tax();
            $this->content_array['SHIPPING_INFO'] = $this->get_shipping_info();
        }
    }
    
    
    protected function add_products()
    {
        global $xtPrice;
        $t_products                      = $this->coo_cart->get_products();
        $this->content_array['products'] = [];
        
        $t_price                                               = '';
        $this->content_array['customer_status_allow_checkout'] = $_SESSION['customers_status']['customers_status_show_price'];
        if (sizeof($t_products) > 0) {
            if ($_SESSION['customers_status']['customers_status_show_price'] != '1') {
                $t_price                                                    = '--';
                $this->content_array['customer_status_allow_checkout_info'] = NOT_ALLOWED_TO_SEE_PRICES;
            }
        }
        
        for ($i = 0, $n = sizeof($t_products); $i < $n; $i++) {
            
            $url = xtc_href_link(FILENAME_PRODUCT_INFO,
                                 xtc_product_link($t_products[$i]['id'], $t_products[$i]['name']));
            
            // Customizer product
            if (strpos($t_products[$i]['id'], '}0') !== false) {
                $url = xtc_href_link(FILENAME_PRODUCT_INFO,
                                     xtc_product_link($t_products[$i]['id'], $t_products[$i]['name']) . '&no_boost=1');
            }
            
            $price = (double)$t_products[$i]['quantity'] * (double)$t_products[$i]['price'];
            
            $vpe                               = get_products_vpe_array($t_products[$i]['id'],
                                                                        $t_products[$i]['price'],
                                                                        [],
                                                                        0);
    
            $sellingUnit                       = $this->getSellingUnitByProductArray($t_products[$i]);
            $this->content_array['products'][] = [
                'QTY'   => $sellingUnit->selectedQuantity()->value(),
                'LINK'  => $url,
                'NAME'  => $sellingUnit->productInfo()->name()->value(),
                'IMAGE' => $this->getMainProductImage($t_products[$i]),
                'PRICE' => (strlen(trim($t_price)) > 0 ? $t_price : $xtPrice->xtcFormat($price, true)),
                'VPE'   => $vpe['vpe_text'] ?? '',
                'UNIT'  => $t_products[$i]['unit_name']
            ];
        }
        $this->content_array['PRODUCTS'] = sizeof($this->content_array['products']);
    }
    
    
    protected function add_total()
    {
        global $xtPrice;
        $total = $this->coo_cart->show_total();
        $discount = 0;
        
        if ($this->customers_status_ot_discount_flag == 1 && $this->customers_status_ot_discount != 0) {
            if ($this->customers_status_show_price_tax == 0 && $this->customers_status_add_tax_ot == 1) {
                $price = $total - $this->coo_cart->show_tax(false);
            } else {
                $price = $total;
            }
            $discount = $xtPrice->xtcGetDC($price, $this->customers_status_ot_discount);
            
            $this->content_array['discount'] = [
                'rate'  => round($this->customers_status_ot_discount, 2) . '% ' . SUB_TITLE_OT_DISCOUNT,
                'price' => '-' . $xtPrice->xtcFormat($discount, true)
            ];
        }
        
        if ($this->customers_status_show_price == '1') {
            if ($this->customers_status_show_price_tax == 0 && $this->customers_status_add_tax_ot == 0) {
                $total -= $discount;
            }
            if ($this->customers_status_show_price_tax == 0 && $this->customers_status_add_tax_ot == 1) {
                $total = $total - $this->coo_cart->show_tax(false) - $discount;
            }
            if ($this->customers_status_show_price_tax == 1) {
                $total -= $discount;
            }
            
            $this->content_array['TOTAL'] = $xtPrice->xtcFormat($total, true);
        }
    }
    
    
    protected function add_tax()
    {
        //GM_MOD:
        if (gm_get_conf('TAX_INFO_TAX_FREE') == 'true') {
            $gm_cart_tax_info = GM_TAX_FREE . '<br />';
        } else {
            $gm_cart_tax_info = $this->coo_cart->show_tax();
        }
        //GM_MOD:
        $this->content_array['UST'] = $gm_cart_tax_info;
    }
    
    
    protected function get_shipping_info()
    {
        global $main;
        
        $t_shipping_info = '';
        
        if (SHOW_SHIPPING == 'true') {
            $t_shipping_info = $main->getShippingLink(true);
        }
        
        return $t_shipping_info;
    }
    
    
    protected function show_products_count()
    {
        return gm_get_conf('SHOW_PRODUCTS_COUNT');
    }
    
    
    public function set_cart_head_template()
    {
        $this->set_content_template('layout_header_cart.html');
    }
    
    
    /**
     * @param array $product
     *
     * @return string
     */
    protected function getMainProductImage(array $product): string
    {
        $image = ProductMainImageProvider::getImage($product['id']);
        return $image === null ? '' : $image->thumbNail()->value();
    }
    
    
    /**
     * @param array $product
     *
     * @return SellingUnitInterface
     */
    protected function getSellingUnitByProductArray(array $product): SellingUnitInterface
    {
        global $xtPrice;
    
        $productId = (string)$product['id'];
    
        if (isset($this->sellingUnits[$productId])) {
        
            return $this->sellingUnits[$productId];
        }
    
        $sellingUnitId    = $this->getSellingUnitIdByProductArray($product);
        $productOrigin    = $this->createProduct($sellingUnitId);
        $quantityFloat    = (float)gm_convert_qty($product['quantity'], true);
        $selectedQuantity = new SelectedQuantity($quantityFloat);
    
        return $this->sellingUnits[$productId] = $this->sellingUnitReadService()
            ->getSellingUnitBy($sellingUnitId, $productOrigin, $xtPrice, $selectedQuantity);
    }
    
    /**
     * @param array $product
     *
     * @return SellingUnitId
     */
    protected function getSellingUnitIdByProductArray(array $product): SellingUnitId
    {
        $productId  = $product['id'];
        $languageId = new LanguageId((int)$_SESSION['languages_id']);
        
        if (is_string($productId)) {
    
            return $this->sellingUnits[$productId] = $this->sellingUnitIdFactory()
                ->createFromProductString($productId, $languageId);
        }
    
        return $this->sellingUnits[(string)$productId] = $this->sellingUnitIdFactory()
            ->createFromCustom('product_id', (string)$productId, $languageId);
    }
    
    /**
     * @return Application
     */
    protected function application(): Application
    {
        return LegacyDependencyContainer::getInstance();
    }
    
    
    /**
     * @return SellingUnitReadServiceInterface
     */
    protected function sellingUnitReadService(): SellingUnitReadServiceInterface
    {
        return $this->application()->get(SellingUnitReadServiceInterface::class);
    }
    
    
    /**
     * @return SellingUnitIdFactoryInterface
     */
    protected function sellingUnitIdFactory(): SellingUnitIdFactoryInterface
    {
        return $this->application()->get(SellingUnitIdFactoryInterface::class);
    }
    
    /**
     * @param SellingUnitId $sellingUnitId
     *
     * @return product_ORIGIN
     */
    protected function createProduct(SellingUnitId $sellingUnitId): ProductDataInterface
    {
        MainFactory::load_origin_class('Product');
        
        $product = new Product($sellingUnitId->productId()->value());
        
        /** @var ProductDataInterface $product */
        return $product;
    }
}
