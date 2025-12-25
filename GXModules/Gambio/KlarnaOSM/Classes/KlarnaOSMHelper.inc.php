<?php
/* --------------------------------------------------------------
   KlarnaOSMHelper.inc.php 2022-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaOSMHelper
{
    /**
     * Returns true if KlarnaOSM module is installed and active.
     *
     * @return bool
     */
    public static function isModuleInstalledAndActive()
    {
        static $installedAndActive = null;
        if (!is_null($installedAndActive)) {
            return $installedAndActive;
        }
        
        $moduleInstalled = filter_var(gm_get_conf('MODULE_CENTER_KLARNAOSM_INSTALLED'),
                                      FILTER_VALIDATE_BOOLEAN);
        if ($moduleInstalled === false) {
            $installedAndActive = false;
            
            return false;
        }
        
        $configuration      = MainFactory::create('KlarnaOSMConfigurationStorage');
        $installedAndActive = (bool)$configuration->get('active');
        
        return $installedAndActive;
    }
    
    
    /**
     * Returns 2-character ISO code for store country
     *
     * @return string
     */
    public static function getStoreCountry()
    {
        static $storeCountryIso = null;
        if (!is_null($storeCountryIso)) {
            return $storeCountryIso;
        }
        /** @var CountryService $countryService */
        $countryService  = StaticGXCoreLoader::getService('Country');
        $storeCountry    = $countryService->getCountryById(new IdType((int)STORE_COUNTRY));
        $storeCountryIso = (string)$storeCountry->getIso2();
        
        return $storeCountryIso;
    }
    
    
    /**
     * Returns current cart total, i. e. whatever ot_total’s value is
     *
     * @return float
     */
    public static function getCartTotalAmount()
    {
        $globalsOrder = isset($GLOBALS['order']) ? $GLOBALS['order'] : null;
        require_once DIR_FS_CATALOG . 'includes/classes/order.php';
        /** @var \order_ORIGIN $order */
        $order = new order();
        if (!isset($order->info['tax_groups'])) {
            $order->info['tax_groups'] = [];
        }
        $GLOBALS['order'] = $order;
        /** @var \order_total_ORIGIN $order_total_modules */
        $order_total_modules = new order_total();
        $order_total_modules->collect_posts();
        $order_total_modules->pre_confirmation_check();
        $totals = $order_total_modules->process();
        $amount = $order->info['total'];
        
        foreach ($totals as $total) {
            if ($total['code'] === 'ot_total') {
                $amount = $total['value'];
            }
        }
        $GLOBALS['order'] = $globalsOrder;
        
        return $amount;
    }
    
    
    /**
     * Modifies an OSM snippet’s locale attribute to match current customer’s country and language.
     *
     * @param string $snippet
     *
     * @return string
     */
    public static function setSnippetLocale($snippet)
    {
        $languageCode = isset($_SESSION['language_code']) ? $_SESSION['language_code'] : @constant('DEFAULT_LANGUAGE');
        $country      = isset($_SESSION['customer_country_iso']) ? $_SESSION['customer_country_iso'] : self::getStoreCountry();
        $locale       = "{$languageCode}-{$country}";
        $snippet      = preg_replace('/data-locale="\w{2}-\w{2}"/',
                                     'data-locale="' . $locale . '"',
                                     $snippet);
        
        return $snippet;
    }
    
    
    /**
     * Modifies an OSM snippet’s purchase-amount attribute to match the cart total, optionally adding an amount.
     *
     * The $additionalAmount is used on product pages to add that product’s price.
     *
     * @param string $snippet
     * @param float  $additionalAmount
     *
     * @return string
     */
    public static function setSnippetPurchaseAmount($snippet, $additionalAmount = 0)
    {
        $cartTotal      = self::getCartTotalAmount();
        $purchaseAmount = (int)round($cartTotal * 100) + (int)round($additionalAmount * 100);
        $snippet        = str_replace('data-purchase-amount=""',
                                      'data-purchase-amount="' . $purchaseAmount . '"',
                                      $snippet);
        
        return $snippet;
    }
    
}
