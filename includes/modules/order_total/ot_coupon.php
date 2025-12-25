<?php
/* --------------------------------------------------------------
  ot_coupon.php 2023-09-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(ot_coupon.php,v 1.1.2.37.3); www.oscommerce.com
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_coupon.php 1322 2005-10-27 13:58:22Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once(DIR_FS_INC . 'xtc_get_currencies_values.inc.php');

class ot_coupon_ORIGIN
{
    public $code;
    public $header;
    public $title;
    public $description;
    public $user_prompt;
    public $enabled;
    public $sort_order;
    public $include_shipping;
    public $include_tax;
    public $calculate_tax;
    public $tax_class;
    public $credit_class;
    public $output;
    public $deduction = 0;
    public $coupon_code;
    
    /**
     * @var xtcPrice_ORIGIN|null
     */
    protected $xtcPrice;
    
    
    public function __construct()
    {
        $this->code             = 'ot_coupon';
        $this->header           = defined('MODULE_ORDER_TOTAL_COUPON_HEADER') ? MODULE_ORDER_TOTAL_COUPON_HEADER : '';
        $this->title            = defined('MODULE_ORDER_TOTAL_COUPON_TITLE') ? MODULE_ORDER_TOTAL_COUPON_TITLE : '';
        $this->description      = defined('MODULE_ORDER_TOTAL_COUPON_DESCRIPTION') ? MODULE_ORDER_TOTAL_COUPON_DESCRIPTION : '';
        $this->user_prompt      = '';
        $this->enabled          = defined('MODULE_ORDER_TOTAL_COUPON_STATUS') ? MODULE_ORDER_TOTAL_COUPON_STATUS : 'false';
        $this->sort_order       = defined('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER') ? MODULE_ORDER_TOTAL_COUPON_SORT_ORDER : '0';
        $this->include_shipping = defined('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING') ? MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING : 'false';
        $this->include_tax      = defined('MODULE_ORDER_TOTAL_COUPON_INC_TAX') ? MODULE_ORDER_TOTAL_COUPON_INC_TAX : 'true';
        $this->calculate_tax    = defined('MODULE_ORDER_TOTAL_COUPON_CALC_TAX') ? MODULE_ORDER_TOTAL_COUPON_CALC_TAX : 'true';
        $this->tax_class        = defined('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS') ? MODULE_ORDER_TOTAL_COUPON_TAX_CLASS : '0';
        $this->credit_class     = true;
        $this->output           = [];
    
        // skip MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING adjustment, if sort order will be updated with this request
        if (isset($_POST['configuration']['configuration/MODULE_ORDER_TOTAL_COUPON_SORT_ORDER'])
            && $_POST['configuration']['configuration/MODULE_ORDER_TOTAL_COUPON_SORT_ORDER'] !== $this->sort_order) {
            return;
        }
        
        $positionOtCoupon   = strpos(MODULE_ORDER_TOTAL_INSTALLED, $this->code);
        $positionOtShipping = strpos(MODULE_ORDER_TOTAL_INSTALLED, 'ot_shipping.php');
        if ($positionOtCoupon < $positionOtShipping) {
            $this->include_shipping = 'false';
        } else {
            $this->include_shipping = 'true';
        }
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->update('gx_configurations',
                    ['value' => $this->include_shipping],
                    ['key' => 'configuration/MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING']);
    }
    
    
    public function process(): void
    {
        if (!$this->isCouponValid()) {
            return;
        }
        
        $totalAmountToBeDiscounted = $this->get_order_total();
        $deduction                 = $this->calculate_credit($totalAmountToBeDiscounted);
        $taxDeduction              = $this->calculate_tax_deduction($totalAmountToBeDiscounted,
                                                                    $deduction,
                                                                    $this->calculate_tax);
        
        if ($deduction > 0) {
            if (!$this->displayedPricesIncludeTax() && $this->isCustomerGroupTaxDisplayEnabled()
                && $this->doesCouponIncludeTax()) {
                $deduction -= $taxDeduction;
            } elseif ($this->displayedPricesIncludeTax() && $this->isCustomerGroupTaxDisplayEnabled()
                      && !$this->doesCouponIncludeTax()) {
                $deduction += $taxDeduction;
            }
            
            $this->deduction = $deduction;
    
            $this->updateOrder($deduction);
            $this->buildOutput($deduction);
        }
    }
    
    
    public function get_order_total()
    {
        return $this->calculateAmountToBeDiscounted();
    }
    
    
    public function calculate_credit($totalAmountToBeDiscounted)
    {
        return $this->calculateDeduction($totalAmountToBeDiscounted);
    }
    
    
    public function calculate_tax_deduction($totalAmountToBeDiscounted, $couponValue, $method)
    {
        return $this->calculateTaxDeduction($totalAmountToBeDiscounted, $couponValue, strtolower($method));
    }
    
    
    /**
     * @param $product_id
     *
     * @return float|int
     * @deprecated not used anymore since GX 4.5
     *
     */
    public function get_product_price($product_id)
    {
        $total_price        = 0.0;
        $qty                = $_SESSION['cart']->contents[$product_id]['qty'];
        $basket_products_id = $product_id;
        $products_id        = xtc_get_prid($product_id);
        
        // products price
        $product_query = xtc_db_query('SELECT
                products_id,
                products_price,
                products_tax_class_id,
                products_weight
            FROM ' . TABLE_PRODUCTS . "
            WHERE products_id='" . (int)$products_id . "'");
        if (xtc_db_num_rows($product_query) > 0) {
            $product = xtc_db_fetch_array($product_query);
            $prid    = $product['products_id'];
            
            if ($this->doesCouponIncludeTax()) {
                $productsTaxClassId = $product['products_tax_class_id'];
            } else {
                $productsTaxClassId = 0;
            }
            $total_price += $qty * $GLOBALS['xtPrice']->xtcGetPrice($basket_products_id,
                                                                    false,
                                                                    1,
                                                                    $productsTaxClassId,
                                                                    $product['products_price'],
                                                                    1,
                                                                    0,
                                                                    true,
                                                                    true);
            
            $products_tax = $GLOBALS['xtPrice']->TAX[$product['products_tax_class_id']];
            
            // attributes price
            if (isset($_SESSION['cart']->contents[$product_id]['attributes'])) {
                foreach ($_SESSION['cart']->contents[$product_id]['attributes'] as $option => $value) {
                    $attribute_price_query = xtc_db_query('SELECT
                            options_values_price,
                            price_prefix
                        FROM ' . TABLE_PRODUCTS_ATTRIBUTES . "
                        WHERE
                            products_id = '" . $prid . "' AND
                            options_id = '" . $option . "' AND
                            options_values_id = '" . $value . "'");
                    $attribute_price       = xtc_db_fetch_array($attribute_price_query);
                    
                    if ($attribute_price['price_prefix'] === '+') {
                        if ($this->doesCouponIncludeTax()) {
                            $total_price += $qty * ($attribute_price['options_values_price']
                                                    + xtc_calculate_tax($attribute_price['options_values_price'],
                                                                        $products_tax));
                        } else {
                            $total_price += $qty * $attribute_price['options_values_price'];
                        }
                    } elseif ($this->doesCouponIncludeTax()) {
                        $total_price -= $qty * ($attribute_price['options_values_price']
                                                + xtc_calculate_tax($attribute_price['options_values_price'],
                                                                    $products_tax));
                    } else {
                        $total_price -= $qty * $attribute_price['options_values_price'];
                    }
                }
            }
        }
        
        if ((int)$_SESSION['customers_status']['customers_status_ot_discount_flag'] === 1) {
            $total_price -= $total_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount'];
        }
        
        if ($this->include_shipping === 'true') {
            $total_price += $GLOBALS['order']->info['shipping_cost'];
        }
        
        return $total_price;
    }
    
    
    /**
     * @param $product_id
     *
     * @return float|int
     * @deprecated not used anymore since GX 4.5
     *
     */
    public function product_price($product_id)
    {
        $total_price = $this->get_product_price($product_id);
        
        if (strtolower($this->include_shipping) === 'true') {
            $total_price -= $GLOBALS['order']->info['shipping_cost'];
        }
        
        return $total_price;
    }
    
    
    /**
     * @return string
     */
    public function check()
    {
        if (!isset($this->check)) {
            $check_query = xtc_db_query('SELECT `value` FROM `gx_configurations`'
                                        . " WHERE `key` = 'configuration/MODULE_ORDER_TOTAL_COUPON_STATUS'");
            $this->check = xtc_db_num_rows($check_query);
        }
        
        return $this->check;
    }
    
    
    /**
     * @return string[]
     */
    public function keys(): array
    {
        return [
            'configuration/MODULE_ORDER_TOTAL_COUPON_STATUS',
            'configuration/MODULE_ORDER_TOTAL_COUPON_SORT_ORDER',
            'configuration/MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING',
            'configuration/MODULE_ORDER_TOTAL_COUPON_INC_TAX',
            'configuration/MODULE_ORDER_TOTAL_COUPON_CALC_TAX',
            'configuration/MODULE_ORDER_TOTAL_COUPON_TAX_CLASS',
            'configuration/MODULE_ORDER_TOTAL_COUPON_SHOW_INFO',
        ];
    }
    
    
    public function install(): void
    {
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_COUPON_STATUS', 'true', '1', 'switcher')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', '70', '2')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 'true', '5', 'switcher')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_COUPON_INC_TAX', 'true', '6','switcher')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'None', '7','tax-calculation-mode')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_COUPON_TAX_CLASS', '0', '0', 'tax-class')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_COUPON_SHOW_INFO', 'false', '9','switcher')");
        
        xtc_db_query("UPDATE `gx_configurations` SET `value` = 'true' WHERE `key` = 'configuration/MODULE_ORDER_TOTAL_GV_INC_SHIPPING'");
    }
    
    
    public function remove()
    {
        $keys       = '';
        $keys_array = $this->keys();
        
        for ($i = 0, $iMax = sizeof($keys_array); $i < $iMax; $i++) {
            $keys .= "'" . $keys_array[$i] . "',";
        }
        
        $keys = substr($keys, 0, -1);
        
        xtc_db_query("DELETE FROM `gx_configurations` WHERE `key` IN (" . $keys . ")");
    }
    
    
    public function apply_credit()
    {
        $insert_id = $GLOBALS['insert_id'];
        
        if ($this->deduction > 0) {
            $t_ip = '';
            if ((bool)gm_get_conf('GM_LOG_IP') === true && (bool)gm_get_conf('GM_CONFIRM_IP') === false) {
                $t_ip = xtc_get_ip_address();
            }
            
            xtc_db_query("INSERT INTO " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, redeem_date, redeem_ip, customer_id, order_id)
							VALUES ('" . $this->getCouponId() . "', now(), '" . $t_ip . "', '"
                         . $_SESSION['customer_id'] . "', '" . $insert_id . "')");
        }
        
        $this->unsetCouponId();
    }
    
    
    /**
     * @return false
     */
    public function selection_test()
    {
        return false;
    }
    
    
    /**
     * @param float $order_total
     *
     * @return float
     */
    public function pre_confirmation_check($order_total)
    {
        return $this->calculate_credit($order_total);
    }
    
    
    /**
     * @return string
     */
    public function use_credit_amount()
    {
        return '';
    }
    
    
    /**
     * @return false
     */
    public function credit_selection()
    {
        return false;
    }
    
    
    public function collect_posts()
    {
        return;
        // processing for $_POST['gv_redeem_code'] used to be here; not used anymore.
    }
    
    
    /**
     * @param $i
     *
     * @return false
     */
    public function update_credit_account($i)
    {
        return false;
    }
    
    
    /**
     * @return float
     */
    protected function calculateAmountToBeDiscounted(): float
    {
        if (!$this->isCouponValid()) {
            return 0;
        }
        
        $amountToBeDiscounted = 0;
        
        $products = $this->getFilteredProducts();
        
        foreach ($products as $product) {
            $finalPrice           = $this->calculateProductPrice($product);
            $amountToBeDiscounted += $finalPrice;
        }
        
        if ($this->shouldShippingCostsBeIncluded()) {
            $amountToBeDiscounted += $this->getShippingCosts();
        }
        
        return $amountToBeDiscounted;
    }
    
    
    /**
     * @param float $totalAmountToBeDiscounted
     *
     * @return float
     */
    protected function calculateDeduction(float $totalAmountToBeDiscounted): float
    {
        if (!$this->isCouponValid()) {
            return 0;
        }
        
        $this->setCouponCode();
        
        if (!$this->isMinimumOrderValueReached()) {
            return 0;
        }
        
        if (!$this->isPercentageDiscount()) {
            return $this->calculateDeductionForFixedDiscount($totalAmountToBeDiscounted);
        }
        
        return $this->calculateDeductionForPercentageDiscount($totalAmountToBeDiscounted);
    }
    
    
    /**
     * @param float  $totalAmountToBeDiscounted
     * @param float  $couponValue
     * @param string $method
     *
     * @return float
     */
    protected function calculateTaxDeduction(
        float $totalAmountToBeDiscounted,
        float $couponValue,
        string $method
    ): float {
        if (empty($couponValue) || $method === 'none' || !$this->isCouponValid()
            || !$this->isMinimumOrderValueReached()) {
            return 0;
        }
        
        $taxDeduction   = 0;
        $couponPortions = $this->calculateCouponPortions($method, $couponValue, $totalAmountToBeDiscounted);
        
        foreach ($couponPortions as $taxClassId => $couponPortionValue) {
            if ($this->doesCouponIncludeTax()) {
                $couponPortionTax = $this->calculateIncludedTax($couponPortionValue, $this->getTaxRate($taxClassId));
            } else {
                $couponPortionTax = $this->calculateTax($couponPortionValue, $this->getTaxRate($taxClassId));
            }
            $taxDeduction += $couponPortionTax;
            
            if (!isset($GLOBALS['order']->info['tax_groups'][$this->getTaxDescription($taxClassId)])) {
                $GLOBALS['order']->info['tax_groups'][$this->getTaxDescription($taxClassId)] = 0.0;
            }
            
            $GLOBALS['order']->info['tax_groups'][$this->getTaxDescription($taxClassId)] -= $couponPortionTax;
            $GLOBALS['order']->info['tax']                                               -= $couponPortionTax;
            
            if(round($GLOBALS['order']->info['tax_groups'][$this->getTaxDescription($taxClassId)], 2) === 0.0) {
                $GLOBALS['order']->info['tax_groups'][$this->getTaxDescription($taxClassId)] = 0.0;
            }
    
            if(round($GLOBALS['order']->info['tax'], 2) === 0.0) {
                $GLOBALS['order']->info['tax_groups'][$this->getTaxDescription($taxClassId)] = 0.0;
            }
        }
        
        return $taxDeduction;
    }
    
    
    /**
     * @param IdType $couponID
     *
     * @return array|null
     */
    protected function getCouponDetails(IdType $couponID): ?array
    {
        $couponDetails = [];
        $query         = 'SELECT * FROM `coupons` WHERE `coupon_id` = ' . $couponID->asInt();
        $result        = xtc_db_query($query);
        if (xtc_db_num_rows($result)) {
            $couponDetails = xtc_db_fetch_array($result);
        }
        
        return $couponDetails;
    }
    
    
    /**
     * @param string $idListString
     *
     * @return array
     */
    protected function parseIdList(string $idListString): array
    {
        $idListString = preg_replace('/[^0-9,]/', '', $idListString);
        $idList       = explode(',', $idListString);
        $idList       = array_filter($idList);
        $idList       = array_unique($idList);
        $idList       = array_map(static function ($element) {
            return (int)$element;
        },
            $idList);
        
        return $idList;
    }
    
    
    /**
     * @param IdType $productsId
     *
     * @return array
     * @deprecated not used anymore since GX 4.5
     *
     */
    protected function getProductCategoriesIds(IdType $productsId): array
    {
        $db                   = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $productCategoriesIds = [];
        $categoriesRow        = $db->get_where('categories_index', ['products_id' => $productsId->asInt()])
            ->row_array();
        if (!empty($categoriesRow)) {
            preg_match_all('-(\d+)-', $categoriesRow['categories_index'], $matches);
            foreach ($matches[1] as $categoryId) {
                if ((int)$categoryId !== 0) {
                    $productCategoriesIds[] = (int)$categoryId;
                }
            }
        }
        
        return $productCategoriesIds;
    }
    
    
    /**
     * @return int
     */
    protected function getShippingTaxClassId(): int
    {
        $shippingModuleName    = explode('_', $GLOBALS['order']->info['shipping_class'])[0];
        $shipping_tax_class_id = 0;
        if (isset($GLOBALS[$shippingModuleName], $GLOBALS[$shippingModuleName]->tax_class)) {
            $shipping_tax_class_id = $GLOBALS[$shippingModuleName]->tax_class;
        } elseif (defined('MODULE_SHIPPING_' . strtoupper($shippingModuleName) . '_TAX_CLASS')) {
            $shipping_tax_class_id = constant('MODULE_SHIPPING_' . strtoupper($shippingModuleName) . '_TAX_CLASS');
        }
        
        return (int)$shipping_tax_class_id;
    }
    
    
    /**
     * @return string
     */
    protected function getTaxDescriptionPrefix(): string
    {
        return $_SESSION['customers_status']['customers_status_show_price_tax'] === '1' ? TAX_ADD_TAX : TAX_NO_TAX;
    }
    
    
    /**
     * @param int $taxClassId
     *
     * @return float
     */
    protected function getTaxRate(int $taxClassId): float
    {
        $taxRate = 0;
        if (!empty($taxClassId)) {
            $taxRate = xtc_get_tax_rate($taxClassId,
                                        $GLOBALS['order']->delivery['country']['id'],
                                        $GLOBALS['order']->delivery['zone_id']);
        }
        
        return $taxRate;
    }
    
    
    /**
     * @param int $taxClassId
     *
     * @return string
     */
    protected function getTaxDescription(int $taxClassId): string
    {
        $taxDescription = '';
        if (!empty($taxClassId)) {
            $taxDescription .= $this->getTaxDescriptionPrefix() . xtc_get_tax_description($taxClassId,
                                                                                          $GLOBALS['order']->delivery['country']['id'],
                                                                                          $GLOBALS['order']->delivery['zone_id']);
        }
        
        return $taxDescription;
    }
    
    
    /**
     * @param float $price
     * @param float $taxRate
     *
     * @return float
     */
    protected function calculateIncludedTax(float $price, float $taxRate): float
    {
        return (($price / (1 + $taxRate / 100)) - $price) * -1;
    }
    
    
    /**
     * @param float $price
     * @param float $taxRate
     *
     * @return float
     */
    protected function calculateTax(float $price, float $taxRate): float
    {
        return $price * $taxRate / 100;
    }
    
    
    /**
     * @param float $price
     * @param float $taxRate
     *
     * @return float
     */
    protected function addTax(float $price, float $taxRate): float
    {
        return $price * (1 + ($taxRate / 100));
    }
    
    
    /**
     * @param float $price
     * @param float $taxRate
     *
     * @return float
     */
    protected function removeTax(float $price, float $taxRate): float
    {
        return $price / ((100 + $taxRate) / 100);
    }
    
    
    /**
     * @param float $total
     * @param float $portion
     *
     * @return float
     */
    protected function calculateRatio(float $total, float $portion): float
    {
        if ($this->isPercentageDiscount()) {
            return $this->getCouponValue() / 100;
        }
        
        if ($total === 0.0) {
            return 0.0;
        }
        
        return 100 / $total * $portion / 100;
    }
    
    
    /**
     * @param array $products
     * @param array $productIdsWhitelist
     * @param array $categoryIdsWhitelist
     *
     * @return array
     */
    protected function filterProducts(array $products, array $productIdsWhitelist, array $categoryIdsWhitelist): array
    {
        $productIdsWhitelist  = array_map('intval', $productIdsWhitelist);
        $categoryIdsWhitelist = array_map('intval', $categoryIdsWhitelist);
        
        foreach ($products as $key => $product) {
            if (isset($product['model']) && strpos($product['model'], 'GIFT_') !== false) {
                unset($products[$key]);
                continue;
            }
            
            $productId = (int)xtc_get_prid($product['id']);
            
            if (in_array($productId, $productIdsWhitelist, false)) {
                continue;
            } elseif (!empty($productIdsWhitelist) && empty($categoryIdsWhitelist)) {
                unset($products[$key]);
                continue;
            }
            
            $subcategoryIds = [];
            
            $query  = 'SELECT `categories_index` FROM `categories_index` WHERE `products_id` = ' . $productId;
            $result = xtc_db_query($query);
            
            if (xtc_db_num_rows($result) === 1) {
                $row = xtc_db_fetch_array($result);
                preg_match_all('-(\d+)-', $row['categories_index'], $matches);
                foreach ($matches[1] as $categoryId) {
                    if ((int)$categoryId !== 0 || count($matches[1]) === 1) {
                        $subcategoryIds[] = (int)$categoryId;
                    }
                }
            }
            
            if ($this->isCategoryNotInWhitelist($categoryIdsWhitelist, $subcategoryIds)) {
                unset($products[$key]);
            }
        }
        
        return array_values($products);
    }
    
    
    /**
     * @return bool
     */
    protected function isCouponActive(): bool
    {
        $coupon = $this->getCoupon();
        
        return isset($coupon['coupon_active']) && $coupon['coupon_active'] === 'Y';
    }
    
    
    /**
     * @return bool
     */
    protected function shouldShippingCostsBeIncluded(): bool
    {
        return strtolower($this->include_shipping) === 'true';
    }
    
    
    /**
     * @return float
     */
    protected function getShippingCosts(): float
    {
        $shippingCosts = $this->getXtcPrice()->xtcCalculateCurr($GLOBALS['order']->info['shipping_cost']);
        
        // check if tax is not included yet
        if ($this->doShippingCostsIncludeNoTax() && $this->considerTaxForAmountToBeDiscounted()) {
            $shippingCosts = $this->addTax($shippingCosts, $this->getTaxRate($this->getShippingTaxClassId()));
        } elseif (!$this->doShippingCostsIncludeNoTax() && !$this->considerTaxForAmountToBeDiscounted()) {
            $shippingCosts = $this->removeTax($shippingCosts, $this->getTaxRate($this->getShippingTaxClassId()));
        }
        
        return $shippingCosts;
    }
    
    
    /**
     * @return float
     */
    protected function getOrderTotalDiscountAsPercentage(): float
    {
        if (!empty($_SESSION['customers_status']['customers_status_ot_discount_flag'])) {
            return (double)$_SESSION['customers_status']['customers_status_ot_discount'];
        }
        
        return 0;
    }
    
    
    protected function applyTotalOrderValueDiscount(float $price): float
    {
        $price -= $price * $this->getOrderTotalDiscountAsPercentage() / 100;
        
        return $price;
    }
    
    
    /**
     * @return bool
     */
    protected function displayedPricesIncludeTax(): bool
    {
        return !empty($_SESSION['customers_status']['customers_status_show_price_tax']);
    }
    
    
    /**
     * @return int
     */
    protected function getCouponId(): int
    {
        return (int)($_SESSION['cc_id'] ?? 0);
    }
    
    
    protected function unsetCouponId(): void
    {
        if (isset($_SESSION['cc_id'])) {
            unset($_SESSION['cc_id']);
        }
    }
    
    
    /**
     * @return float
     */
    protected function getCouponValue(): float
    {
        $coupon = $this->getCoupon();
        if (empty($coupon)) {
            return 0;
        }
        
        if ($coupon['coupon_type'] === 'P') {
            return (double)$coupon['coupon_amount'];
        }
        
        $couponValue = round($this->getXtcPrice()->xtcCalculateCurr($coupon['coupon_amount']), 2);
        $couponValue += $this->isCouponRefundingShippingCosts() ? $this->getShippingCosts() : 0;
        
        return $couponValue;
    }
    
    
    /**
     * @return float
     */
    protected function getMinimumOrderValue(): float
    {
        $coupon = $this->getCoupon();
        
        return empty($coupon) ? 0 : round($this->getXtcPrice()->xtcCalculateCurr($coupon['coupon_minimum_order']), 2);
    }
    
    
    /**
     * @return bool
     */
    protected function isPercentageDiscount(): bool
    {
        $coupon = $this->getCoupon();
        
        return !empty($coupon) && $coupon['coupon_type'] === 'P';
    }
    
    
    /**
     * @return bool
     */
    protected function isCouponRefundingShippingCosts(): bool
    {
        $coupon = $this->getCoupon();
        
        return !empty($coupon) && $coupon['coupon_type'] === 'S';
    }
    
    
    /**
     * @return bool
     */
    protected function isCouponValid(): bool
    {
        if (empty($this->getCouponId())) {
            return false;
        }
        
        $coupon = $this->getCoupon();
        if (!empty($coupon)) {
            
            if ($this->isCouponActive() === false) {
                
                return false;
            }
            
            //  checking if the minimum order amount was reached with all products in the cart
            $minimumAmount = (float)$coupon['coupon_minimum_order'];
            $cartProducts  = $this->getFilteredProducts();
            $cartTotal     = array_sum(array_column($cartProducts, 'final_price'));
    
            return $cartTotal >= $minimumAmount;
        }
        
        return true;
    }
    
    
    /**
     * @return array
     */
    protected function getCoupon(): array
    {
        return $this->getCouponDetails(new IdType($this->getCouponId()));
    }
    
    
    /**
     * @return array
     */
    protected function getFilteredProducts(): array
    {
        $coupon                  = $this->getCoupon();
        $restrictToProductIds    = $this->parseIdList($coupon['restrict_to_products'] ?? '');
        $restrictToCategoriesIds = $this->parseIdList($coupon['restrict_to_categories'] ?? '');
        
        return $this->filterProducts($GLOBALS['order']->products,
                                     $restrictToProductIds,
                                     $restrictToCategoriesIds);
    }
    
    
    /**
     * @return bool
     */
    protected function isMinimumOrderValueReached(): bool
    {
        return $this->getMinimumOrderValue() <= $this->get_order_total();
    }
    
    
    /**
     * If only one tax rate has to be considered, its tax class id will be returned, otherwise null.
     *
     * @param string $method
     *
     * @return int|null
     */
    protected function checkIfOnlyOneTaxClassIdHasToBeConsidered(string $method): ?int
    {
        if ($method === 'credit note') {
            return (int)$this->tax_class;
        }
        
        $products   = $this->getFilteredProducts();
        $taxClassId = $this->shouldShippingCostsBeIncluded()
                      || $this->isCouponRefundingShippingCosts() ? $this->getShippingTaxClassId() : 0;
        foreach ($products as $product) {
            $productTaxClassId = (int)$product['tax_class_id'];
            if ($taxClassId !== 0 && $productTaxClassId !== 0 && $productTaxClassId !== $taxClassId) {
                return null;
            }
            
            $taxClassId = $productTaxClassId !== 0 ? $productTaxClassId : $taxClassId;
        }
        
        return $taxClassId;
    }
    
    
    /**
     * @param $product
     *
     * @return float
     */
    protected function calculateProductPrice($product): float
    {
        $finalPrice = $product['final_price'];
        if (!$this->displayedPricesIncludeTax() && $this->considerTaxForAmountToBeDiscounted()) {
            $finalPrice = $this->addTax($finalPrice, $product['tax']);
        } elseif ($this->displayedPricesIncludeTax() && !$this->considerTaxForAmountToBeDiscounted()) {
            $finalPrice = $this->removeTax($finalPrice, $product['tax']);
        }
        
        return $this->applyTotalOrderValueDiscount($finalPrice);
    }
    
    
    /**
     * @return xtcPrice_ORIGIN
     */
    protected function getXtcPrice(): xtcPrice_ORIGIN
    {
        if ($this->xtcPrice === null) {
            $this->xtcPrice = MainFactory::create('xtcPrice',
                                                  $_SESSION['currency'],
                                                  (int)$_SESSION['customers_status']['customers_status_id']);
        }
        
        return $this->xtcPrice;
    }
    
    
    /**
     * @return bool
     */
    protected function considerTaxForAmountToBeDiscounted(): bool
    {
        return $this->doesCouponIncludeTax() && $this->isCustomerGroupTaxDisplayEnabled();
    }
    
    
    /**
     * @return bool
     */
    protected function doesCouponIncludeTax(): bool
    {
        return $this->include_tax === 'true';
    }
    
    
    /**
     * @return bool
     */
    protected function doShippingCostsIncludeNoTax(): bool
    {
        return (double)$GLOBALS['order']->info['shipping_cost'] === (double)($_SESSION['shipping']['cost'] ?? 0.0);
    }
    
    
    /**
     * @return bool
     */
    protected function isCustomerGroupTaxDisplayEnabled(): bool
    {
        return (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] === 1;
    }
    
    
    /**
     * @param NonEmptyStringType $input
     * @param LanguageCode       $languageCode
     *
     * @return string
     */
    protected function addQuotes(NonEmptyStringType $input, LanguageCode $languageCode): string
    {
        if ($languageCode->asString() === 'de') {
            $openingQuotationMark = '„';
            $closingQuotationMark = '“';
        } elseif ($languageCode->asString() === 'en') {
            $openingQuotationMark = '“';
            $closingQuotationMark = '”';
        } else {
            $openingQuotationMark = '"';
            $closingQuotationMark = '"';
        }
        
        return $openingQuotationMark . $input->asString() . $closingQuotationMark;
    }
    
    
    /**
     * If products or shipping costs have different tax rates, the coupon value shall be applied proportionately to the
     * totals of the respective tax classes. The corresponding proportions are determined here.
     *
     * @param string $method
     * @param float  $couponValue
     * @param float  $totalAmountToBeDiscounted
     *
     * @return array
     */
    protected function calculateCouponPortions(
        string $method,
        float $couponValue,
        float $totalAmountToBeDiscounted
    ): array {
        $couponPortions = [];
        $taxClassId     = $this->checkIfOnlyOneTaxClassIdHasToBeConsidered($method);
        
        if ($taxClassId !== null) {
            $couponPortions[$taxClassId] = $couponValue;
            
            return $couponPortions;
        }
        
        // handle free shipping
        if ($this->isCouponRefundingShippingCosts()) {
            // handle shipping costs
            $this->addCouponPortion($couponPortions,
                                    $this->getShippingCosts(),
                                    $this->getShippingCosts(),
                                    $this->getShippingCosts(),
                                    $this->getShippingTaxClassId());
            
            // after handling shipping costs, reduce coupon value by shipping costs
            $couponValue -= $this->getShippingCosts();
            
            if ($this->shouldShippingCostsBeIncluded()) {
                // reduce total amount by shipping costs
                $totalAmountToBeDiscounted -= $this->getShippingCosts();
            }
        }
        
        $products = $this->getFilteredProducts();
        
        foreach ($products as $product) {
            $this->addCouponPortion($couponPortions,
                                    $couponValue,
                                    $totalAmountToBeDiscounted,
                                    $this->calculateProductPrice($product),
                                    (int)$product['tax_class_id']);
        }
        
        if ($this->shouldShippingCostsBeIncluded() && !$this->isCouponRefundingShippingCosts()) {
            $this->addCouponPortion($couponPortions,
                                    $couponValue,
                                    $totalAmountToBeDiscounted,
                                    $this->getShippingCosts(),
                                    $this->getShippingTaxClassId());
        }
        
        return $couponPortions;
    }
    
    
    /**
     * @param array $couponPortions
     * @param float $couponValue
     * @param float $totalAmountToBeDiscounted
     * @param float $price
     * @param int   $taxClassId
     */
    protected function addCouponPortion(
        array &$couponPortions,
        float $couponValue,
        float $totalAmountToBeDiscounted,
        float $price,
        int $taxClassId
    ): void {
        if (!isset($couponPortions[$taxClassId])) {
            $couponPortions[$taxClassId] = 0;
        }
        
        if (!$this->isPercentageDiscount()) {
            $couponPortions[$taxClassId] += $couponValue * $this->calculateRatio($totalAmountToBeDiscounted,
                                                                                 $price);
            
            return;
        }
        
        $couponPortions[$taxClassId] += $price * ($this->getCouponValue() / 100);
    }
    
    
    /**
     * @param       $categoryIdsWhitelist
     * @param array $subcategoryIds
     *
     * @return bool
     */
    protected function isCategoryNotInWhitelist($categoryIdsWhitelist, array $subcategoryIds): bool
    {
        return !empty($categoryIdsWhitelist) && count(array_intersect($subcategoryIds, $categoryIdsWhitelist)) === 0;
    }
    
    
    protected function setCouponCode(): void
    {
        $coupon            = $this->getCoupon();
        $this->coupon_code = $coupon['coupon_code'];
    }
    
    
    /**
     * @param float $totalAmountToBeDiscounted
     *
     * @return mixed
     */
    protected function calculateDeductionForFixedDiscount(float $totalAmountToBeDiscounted)
    {
        $couponValue = $this->getCouponValue();
        
        return min($totalAmountToBeDiscounted, $couponValue);
    }
    
    
    /**
     * @param float $totalAmountToBeDiscounted
     *
     * @return mixed
     */
    protected function calculateDeductionForPercentageDiscount(float $totalAmountToBeDiscounted)
    {
        $couponDiscount = 0;
        $products       = $this->getFilteredProducts();
        $couponValue    = $this->getCouponValue();
        
        foreach ($products as $product) {
            $finalPrice     = $this->calculateProductPrice($product);
            $couponDiscount += $finalPrice * $couponValue / 100;
        }
        
        if ($this->shouldShippingCostsBeIncluded()) {
            $couponDiscount += $this->getShippingCosts() * $couponValue / 100;;
        }
        
        return min($totalAmountToBeDiscounted, $couponDiscount);
    }
    
    
    /**
     * @param float $deduction
     */
    protected function updateOrder(float $deduction): void
    {
        $GLOBALS['order']->info['subtotal']  -= round($deduction, 2);
        $GLOBALS['order']->info['total']     -= round($deduction, 2);
        $GLOBALS['order']->info['deduction'] = $deduction;
    }
    
    
    /**
     * @param float $deduction
     */
    protected function buildOutput(float $deduction): void
    {
        $quotedCode     = $this->addQuotes(new NonEmptyStringType($this->coupon_code),
                                           new LanguageCode(new StringType($_SESSION['language_code'])));
        $this->output[] = [
            'title' => sprintf('%s %s:', $this->title, $quotedCode),
            'text'  => '-' . $this->getXtcPrice()->xtcFormat($deduction, true),
            'value' => $deduction * -1,
        ];
    }
}

MainFactory::load_origin_class('ot_coupon');
