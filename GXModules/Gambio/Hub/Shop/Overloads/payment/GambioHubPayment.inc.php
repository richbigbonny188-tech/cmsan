<?php
/* --------------------------------------------------------------
   GambioHubPayment.inc.php 2023-05-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\HubSessionKey;

/**
 * Class GambioHubPayment
 *
 * This overload will fetch the allowed Gambio Hub modules for the payment page.
 */
class GambioHubPayment extends GambioHubPayment_parent
{
    /**
     * @var string
     */
    protected $cashOnDeliveryModuleCode = 'CashOnDeliveryHub';
    
    /**
     * @var string
     */
    protected $cashModuleCode = 'CashHub';
    
    /**
     * @var string
     */
    protected $moneyOrderModuleCode = 'MoneyOrderHub';
    
    /**
     * @var string
     */
    protected $invoiceModuleCode = 'InvoiceHub';
    
    /**
     * @var array
     */
    protected $allowedPaymentModules;
    
    /**
     * Maximum age of a Gambio Hub session in seconds
     */
    const SESSION_MAX_AGE = 3300;
    
    
    /**
     * Module selection method overload.
     *
     * This method will fetch the allowed payment module from Gambio Hub and will display them in the
     * order process module listing.
     *
     * @return array
     */
    public function selection()
    {
        $selectionArray = parent::selection();
        
        if (strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) !== 'true') {
            return $selectionArray;
        }
        
        /* BEGIN check hub session validity */
        $criteria      = '';
        $criteria      .= $_SESSION['cart']->cartID;
        $criteria      .= $_SESSION['shipping']['id'] ?? '';
        $criteria      .= $_SESSION['shipping']['cost'] ?? '';
        $criteria      .= $_SESSION['sendto'];
        $criteria      .= $_SESSION['billto'];
        $criteria      .= isset($_SESSION['order']) ? $_SESSION['order']->info['total'] : '';
        $criteria      .= isset($_SESSION['cot_gv']) ? (string)$_SESSION['cot_gv'] : 'no_got_gv';
        $criteria      .= isset($_SESSION['cc_id']) ? (string)$_SESSION['cc_id'] : 'no_cc_id';
        $criteria      = hash('sha256', $criteria);
        $sessionTooOld = !empty($_SESSION['gambio_hub_session_key_refreshed']) ? (microtime(true)
                                                                                  - $_SESSION['gambio_hub_session_key_refreshed'])
                                                                                 > self::SESSION_MAX_AGE : true;
        
        if ($sessionTooOld
            || (isset($_SESSION['gambio_hub_session_criteria'])
                && $_SESSION['gambio_hub_session_criteria'] !== $criteria)) {
            // reset hub session key for forcing a new session key creation
            $_SESSION['gambio_hub_session_key']           = '';
            $_SESSION['gambio_hub_session_key_refreshed'] = microtime(true);
            $this->allowedPaymentModules                  = null;
        }
        
        $_SESSION['gambio_hub_session_criteria'] = $criteria;
        /* END check hub session validity */
        
        $order = $GLOBALS['order'] ?? null;
        
        if ($order === null || !is_a($order, 'order')) {
            return $selectionArray;
        }
        
        foreach ($selectionArray as $index => $module) {
            if ($module['id'] === 'gambio_hub') {
                unset($selectionArray[$index]);
                break;
            }
        }
        
        $allowedPaymentModules = $this->_getAllowedPaymentModules();
        $hubSelectionArray     = [];
        $languageTextManager   = MainFactory::create('LanguageTextManager', 'hub_phrases', $_SESSION['languages_id']);
        $moduleBlacklist       = [];
        $shippingId            = $_SESSION['shipping']['id'] ?? '';
        if (strpos($shippingId, 'selfpickup') !== false) {
            $moduleBlacklist[] = 'PayPal2Hub';
            $moduleBlacklist[] = 'CashOnDeliveryHub';
        }
        
        $_SESSION['gambio_hub_payments'] = [];
        foreach ($allowedPaymentModules as $module) {
            $shippingDisabledKey = 'disable' . ucfirst($shippingId) . 'Shipping'; // e. g. 'disableSelfpickupShipping'
            $shippingDisabled    = isset($module['configuration'][$shippingDisabledKey])
                                   && $module['configuration'][$shippingDisabledKey] === 'true';
            if ($shippingDisabled || in_array($module['moduleCode'], $moduleBlacklist, true)) {
                continue;
            }
            $modulePrefix = 'GAMBIO_HUB_' . strtoupper($module['moduleCode']) . '_';
            $title        = $languageTextManager->get_text($modulePrefix . 'TITLE');
            $infoText     = $languageTextManager->get_text($modulePrefix . 'INFOTEXT');
            $description  = $languageTextManager->get_text($modulePrefix . 'DESCRIPTION');
            
            if ($title === $modulePrefix . 'TITLE') {
                $title = $module['paymentTitle'];
            }
            if ($infoText === $modulePrefix . 'INFOTEXT') {
                $infoText = $module['paymentInfoText'];
            }
            if ($description === $modulePrefix . 'DESCRIPTION') {
                $description = $module['paymentDescription'];
            }
            
            $_SESSION['gambio_hub_payments'][$module['moduleCode']] = [
                'title'         => $title,
                'info_text'     => $infoText,
                'configuration' => $module['configuration']
            ];
            
            if ($module['moduleCode'] === $this->moneyOrderModuleCode
                && array_key_exists('payTo', $module['configuration'])
                && gm_get_conf('GAMBIO_HUB_MONEY_ORDER_PAY_TO', 'ASSOC', true) !== $module['configuration']['payTo']) {
                // cache HubMoneyOrder PayTo config value for order mail template
                gm_set_conf('GAMBIO_HUB_MONEY_ORDER_PAY_TO', $module['configuration']['payTo']);
            }
            
            $cashOnDeliveryFee = $this->_getCashOnDeliveryFee($module, $order);
            
            $subModules                                                        = $this->extractSubmodules($description);
            $description                                                       = $this->stripSubmodules($description);
            $_SESSION['gambio_hub_payments_submodules'][$module['moduleCode']] = $subModules;
            
            $description = $this->insertExtraData($description);
            
            $hubSelectionEntry = [
                'id'          => 'gambio_hub-' . $module['moduleCode'],
                'module'      => $title,
                'description' => $description,
            ];
            
            // try to find a logo in module’s description
            $matches = [];
            if (preg_match('/<img((\s+src="(?<src>.+?)")|(\s+alt="(?<alt>.+?)")|(\s+class="(?<class>.+?)")|(\s+\w+=".*?"))+\s*\/?>/',
                           $module['paymentDescription'],
                           $matches) !== false) {
                $classes = [];
                if (!empty($matches['class'])) {
                    $classes = explode(' ', $matches['class']);
                }
                if (in_array('hub-logo', $classes, true)) {
                    $hubSelectionEntry['logo_url'] = $matches['src'];
                    $hubSelectionEntry['logo_alt'] = $matches['alt'];
                }
            }
            
            // if present, logo from module configuration supersedes logo from description
            $logoUrlKey = $module['moduleCode'] . 'LogoUrl' . ucfirst($_SESSION['language_code']);
            if (array_key_exists($logoUrlKey, $module['configuration'])) {
                $hubSelectionEntry['logo_url'] = $module['configuration'][$logoUrlKey];
                $logoAltKey                    = $module['moduleCode'] . 'LogoAlt'
                                                 . ucfirst($_SESSION['language_code']);
                if (array_key_exists($logoAltKey, $module['configuration'])) {
                    $hubSelectionEntry['logo_alt'] = $module['configuration'][$logoAltKey];
                }
            }
            
            if ($cashOnDeliveryFee !== '0') {
                $hubSelectionEntry['module_cost'] = '+ ' . $cashOnDeliveryFee;
            }
            
            $hubSelectionEntry   = $this->extendSelection($hubSelectionEntry);
            $hubSelectionArray[] = $hubSelectionEntry;
            
            if (!empty($subModules)) {
                foreach ($subModules as $subModule) {
                    $subModuleEntry      = [
                        'id'          => 'gambio_hub-' . $module['moduleCode'] . '-' . $subModule['submodule_id'],
                        'module'      => $subModule['title'],
                        'info_text'   => $subModule['info_text'],
                        'description' => $subModule['description'],
                        'logo_url'    => $subModule['logo_url'],
                    ];
                    $subModuleEntry      = $this->extendSelection($subModuleEntry);
                    $hubSelectionArray[] = $subModuleEntry;
                }
            }
        }
        
        // unset selected hub payment in session if method is not available anymore
        if (array_key_exists('gambio_hub_selection', $_SESSION)
            && !in_array($_SESSION['gambio_hub_selection'], array_keys($_SESSION['gambio_hub_payments']))) {
            unset($_SESSION['gambio_hub_selection']);
        }
        
        if (count($allowedPaymentModules)) {
            if (!empty($_SESSION['PayPal2Hub']['payerID'])
                && (!empty($_SESSION['PayPal2Hub']['paymentID'])
                    || !empty($_SESSION['PayPal2Hub']['orderID']))) {
                // PayPal ECS mode
                $ecsHubSelectionArray = [];
                foreach ($hubSelectionArray as $selectionModule) {
                    if ($selectionModule['id'] === 'gambio_hub-PayPal2Hub') {
                        $ecsHubSelectionArray[] = $selectionModule;
                    }
                }
                $hubSelectionArray = $ecsHubSelectionArray;
            } else {
                foreach ($selectionArray as $module) {
                    if ($module['id'] === 'paypal3') {
                        array_unshift($hubSelectionArray, $module);
                    } else {
                        $hubSelectionArray[] = $module;
                    }
                }
            }
            
            $selectionArray = $hubSelectionArray;
        }
        
        return $this->removeForbiddenMethodsForDownloads($selectionArray);
    }
    
    
    protected function extractSubmodules($description)
    {
        $pattern     = '/\<script type="application\/json" id="(?\'module_id\'[\w-]+)"\>(?\'submodules_json\'.*?)\<\/script\>/s';
        $description = (string)$description;
        $submodules  = [];
        if (preg_match($pattern, $description, $matches) === 1) {
            $submodulesExtracted = json_decode($matches['submodules_json'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $submodules = $submodulesExtracted;
            }
        }
        
        return $submodules;
    }
    
    
    protected function stripSubmodules($description)
    {
        $pattern             = '/\<script type="application\/json" id="(?\'module_id\'[\w-]+)"\>(?\'submodules_json\'.*?)\<\/script\>/s';
        $description         = (string)$description;
        $strippedDescription = preg_replace($pattern, '', $description);
        
        return $strippedDescription;
    }
    
    
    /**
     * @return array
     */
    protected function _getUnallowedModuleCodes()
    {
        // load unallowed modules into array
        $unallowedModuleCodes = explode(',',
            ($_SESSION['customers_status']['customers_status_payment_unallowed'] ?? '') . ','
                                        . $GLOBALS['order']->customer['payment_unallowed']);
        
        // add unallowed modules/Download
        if ($GLOBALS['order']->content_type == 'virtual' || $GLOBALS['order']->content_type == 'virtual_weight'
            || $GLOBALS['order']->content_type == 'mixed') {
            $unallowedModuleCodes = array_merge($unallowedModuleCodes, explode(',', DOWNLOAD_UNALLOWED_PAYMENT));
        }
        
        // disable payment method $this->cashOnDeliveryModuleName for gift vouchers
        if ($_SESSION['cart']->count_contents_non_virtual() == 0
            && array_search($this->cashOnDeliveryModuleCode, $unallowedModuleCodes) === false) {
            $unallowedModuleCodes[] = $this->cashOnDeliveryModuleCode;
        }
        
        // disable hub cash payment module, if shipping method is not selfpickup
        if (($_SESSION['shipping']['id'] ?? null) !== 'selfpickup_selfpickup') {
            $unallowedModuleCodes[] = $this->cashModuleCode;
        }
        
        // disable hub invoice payment module, if the customer hasn't reached the preconfigured order count
        if ($this->_isMissingRequiredCustomerOrders()) {
            $unallowedModuleCodes[] = $this->invoiceModuleCode;
        }
        
        return $unallowedModuleCodes;
    }
    
    
    /**
     * Checks whether the customer has the required orders count for invoices.
     *
     * @return bool
     */
    protected function _isMissingRequiredCustomerOrders()
    {
        $threshold = (int)gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_INVOICEHUB_REQUIREDORDERSCOUNT');
        
        $ordersCount = (int)StaticGXCoreLoader::getDatabaseQueryBuilder()
            ->get_where('orders', ['customers_id' => $_SESSION['customer_id'] ?? null])
            ->num_rows();
        
        return $ordersCount < $threshold;
    }
    
    
    /**
     * Returns the allowed payment modules using a helper method.
     *
     * @return array
     */
    protected function _getAllowedPaymentModules()
    {
        /** @var LogControl $logControl */
        $logControl       = LogControl::get_instance();
        $profilingLogfile = DIR_FS_CATALOG . 'logfiles/gethubpayment-profiling-' . LogControl::get_secure_token()
                            . '.log';
        $profilingStart   = microtime(true);
        
        try {
            $allowedPaymentModules = $this->_getHubPaymentModules();
        } catch (\Exception $e) {
            try {
                // Start a new session and retry fetching the allowed payment modules.
                $checkoutHelper = MainFactory::create('HubCheckoutHelper');
                
                $checkoutHelper->startSession(HTTP_SERVER . DIR_WS_CATALOG,
                                              new LanguageCode(new StringType(strtoupper(DEFAULT_LANGUAGE))));
                
                $allowedPaymentModules = $this->_getHubPaymentModules();
            } catch (\Exception $e) {
                $logControl->notice('Could not fetch the allowed payment modules: ' . $e->getMessage(), '', 'hub');
                
                // Could not fetch the allowed payment modules.
                $allowedPaymentModules = [];
            }
        }
        
        $profilingEnd        = microtime(true);
        $loggingDatetime     = new DateTime();
        $profilingLogMessage = implode(',', [
            $loggingDatetime->format('Y-m-d H:i:s.u'),
            $loggingDatetime->format('U.u'),
            session_id(),
            $_SESSION['gambio_hub_session_key'] ?? '',
            $_SESSION['customer_id'] ?? '',
            number_format($profilingEnd - $profilingStart, 3, '.', ''),
        ]);
        file_put_contents($profilingLogfile, $profilingLogMessage . "\n", FILE_APPEND);
        
        return $allowedPaymentModules;
    }
    
    
    /**
     * Returns the allowed payment modules.
     *
     * @return array
     */
    protected function _getHubPaymentModules()
    {
        if ($this->allowedPaymentModules !== null) {
            return $this->allowedPaymentModules;
        }
        
        $helper = MainFactory::create('HubCheckoutHelper');
        
        // Hub transactions API client.
        $hubTransactionsApiClient = $helper->createHubTransactionsApiClient(new HubSessionKey($_SESSION['gambio_hub_session_key'] ?? ''));
        
        $cartContent                 = $helper->getCartContent($GLOBALS['order']);
        $customerInformation         = $helper->getCustomerInformation($GLOBALS['order']);
        $hubClientInformation        = $helper->getHubClientInformation();
        $clientSessionInformation    = $helper->getClientSessionInformation($GLOBALS['order']);
        $this->allowedPaymentModules = $hubTransactionsApiClient->getAllowedPaymentModules($cartContent,
                                                                                           $customerInformation,
                                                                                           $hubClientInformation,
                                                                                           $clientSessionInformation,
                                                                                           $this->_getUnallowedModuleCodes());
        
        return $this->allowedPaymentModules;
    }
    
    
    /**
     * Returns cash on delivery fee
     *
     * @param array $module
     * @param order $order
     *
     * @return string
     */
    private function _getCashOnDeliveryFee($module, $order)
    {
        $fee = 0;
        
        if (defined('MODULE_ORDER_TOTAL_COD_FEE_STATUS') && MODULE_ORDER_TOTAL_COD_FEE_STATUS === 'true'
            && $module['moduleCode'] === $this->cashOnDeliveryModuleCode) {
            $country = false;
            $zones   = [];
            
            if (strpos(MODULE_ORDER_TOTAL_COD_FEE_RULES, '|') !== false) {
                $rules    = explode('|', MODULE_ORDER_TOTAL_COD_FEE_RULES);
                $shipping = [];
                
                for ($i = 0; $i < count($rules); $i++) {
                    if ($i % 2 === 0) {
                        $moduleName = $rules[$i];
                    } else {
                        $shipping[$moduleName] = $rules[$i];
                    }
                }
                
                if (isset($_SESSION['shipping']['id'], $shipping[strtok($_SESSION['shipping']['id'], '_')])) {
                    $zones = preg_split('/[:,]/', $shipping[strtok($_SESSION['shipping']['id'], '_')]);
                }
            }
            
            for ($i = 0; $i < count($zones); $i++) {
                if ($zones[$i] === $order->delivery['country']['iso_code_2']) {
                    $fee     = $zones[$i + 1];
                    $country = true;
                    break;
                } elseif ($zones[$i] === '00') {
                    $fee     = $zones[$i + 1];
                    $country = true;
                    break;
                }
                
                $i++;
            }
        }
        
        if ($fee !== 0 && $country) {
            $fee = $GLOBALS['xtPrice']->xtcCalculateCurr($fee);
            
            $tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS,
                                    $order->delivery['country']['id'],
                                    $order->delivery['zone_id']);
            
            if (($_SESSION['customers_status']['customers_status_show_price_tax'] ?? null) === '1') {
                $fee = $GLOBALS['xtPrice']->xtcFormat(xtc_add_tax($fee, $tax), true);
            } else {
                $fee = $GLOBALS['xtPrice']->xtcFormat($fee, true);
            }
        }
        
        return (string)$fee;
    }
    
    
    protected function insertExtraData($description, $extraDataPlaceholder = '<span class="extradata"></span>')
    {
        if (strpos($description, $extraDataPlaceholder) !== false) {
            $isGuest   = ($_SESSION['customers_status']['customer_status_id'] ?? null) === DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
            $extraData = [
                'isGuest' => $isGuest,
            ];
            if (!$isGuest) {
                /** @var OrderReadService $orderRead */
                $orderRead = StaticGXCoreLoader::getService('OrderRead');
                /** @var OrderListItemCollection $orders */
                $orders         = $orderRead->getOrderListByCustomerId(new IdType($_SESSION['customer_id']));
                $previousOrders = $orders->count();
                
                $firstOrderDateTime = new \DateTime();
                /** @var OrderListItem $order */
                foreach ($orders->getIterator() as $order) {
                    $purchaseDateTime = $order->getPurchaseDateTime();
                    if ($purchaseDateTime < $firstOrderDateTime) {
                        $firstOrderDateTime = $purchaseDateTime;
                    }
                }
                $extraData['previousOrders']        = $previousOrders;
                $extraData['firstPurchaseDateTime'] = $firstOrderDateTime->format('c');
            }
            $extraDataJson = json_encode($extraData);
            $iv            = openssl_random_pseudo_bytes(16, $strongCrypto);
            if ($iv === false || $strongCrypto === false) {
                return '';
            }
            $key                  = $_SESSION['gambio_hub_session_key'] ?? '';
            $extraDataEncrypted   = openssl_encrypt($extraDataJson, 'aes-256-cbc', $key, 0, $iv);
            $extraDataValue       = base64_encode($extraDataEncrypted) . '.' . base64_encode($iv);
            $extraDataReplacement = '<input type="hidden" name="extradata" value="' . $extraDataValue . '">';
            $description          = str_replace($extraDataPlaceholder, $extraDataReplacement, $description);
        }
        
        return $description;
    }
    
    
    /**
     * @param array $selectionArray
     *
     * @return array
     */
    protected function removeForbiddenMethodsForDownloads(array $selectionArray): array
    {
        $contentType = $GLOBALS['order']->content_type ?? $_SESSION['cart']->get_content_type();
        if (in_array($contentType, ['virtual', 'virtual_weight', 'mixed'])) {
            $forbiddenPaymentMethods = preg_split('/\s*,\s*/', DOWNLOAD_UNALLOWED_PAYMENT);
            
            foreach ($selectionArray as $key => $item) {
                foreach ($forbiddenPaymentMethods as $forbiddenPaymentMethod) {
                    if (substr($forbiddenPaymentMethod, -3) === 'Hub'
                        && stripos($item['id'], $forbiddenPaymentMethod) === strlen('gambio_hub-')) {
                        unset($selectionArray[$key]);
                        continue(2);
                    }
                }
            }
            
            $selectionArray = array_values($selectionArray);
        }
        
        return $selectionArray;
    }
}
