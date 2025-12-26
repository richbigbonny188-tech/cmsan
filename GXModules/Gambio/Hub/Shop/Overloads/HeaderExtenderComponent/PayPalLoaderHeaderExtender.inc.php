<?php
/* --------------------------------------------------------------
   PayPalLoaderHeaderExtender.inc.php 2023-04-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Http\CurlRequest;

class PayPalLoaderHeaderExtender extends PayPalLoaderHeaderExtender_parent
{
    public function proceed()
    {
        parent::proceed();
        if(strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) !== 'true') {
            return;
        }
        
        $isEcsEnabled    = filter_var(gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_PAYPAL2HUB_ECS'), FILTER_VALIDATE_BOOLEAN);
        $isBannerEnabled = false;
        $bannerPositions = ['CARTBOTTOM', 'PRODUCT'];
        foreach ($bannerPositions as $bannerPosition) {
            $positionLayout = gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_PAYPAL2HUB_INSTALLMENTSBANNER_' . $bannerPosition . '_LAYOUT');
            $positionEnabled = $positionLayout !== null && $positionLayout !== 'none';
            $isBannerEnabled = $isBannerEnabled || $positionEnabled;
        }
        $isCheckoutPayment = strpos(gm_get_env_info('PHP_SELF'), 'checkout_payment.php') !== false;
        
        if ($isEcsEnabled === false && $isBannerEnabled === false && $isCheckoutPayment === false) {
            return;
        }
        
        /** @var \HubPayPalConfiguration $config */
        $config = MainFactory::create('HubPayPalConfiguration');
        $text   = MainFactory::create('LanguageTextManager', 'gambio_hub_paypal', $_SESSION['languages_id']);
        $payPalText = [
            'ecsNote'           => $text->get_text('ecs_note'),
            'paypalUnavailable' => $text->get_text('paypal_unavailable'),
            'errorContinue'     => $text->get_text('error_continue'),
            'continueToPayPal'  => $text->get_text('continue_to_paypal'),
            'errorCheckData'    => $text->get_text('error_check_data'),
        ];
        
        $paramsScript = '<script>';
        $paramsScript .= 'var payPalText = ' . json_encode($payPalText) . ';';
    
        /** @var \HubPayPalButtonSettings $buttonSettings */
        $buttonSettings = MainFactory::create('HubPayPalButtonSettings', $config);
        if (empty($_SESSION['customer_id'])
            && $_SESSION['customers_status']['customers_status_id'] === DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
            $checkoutUrl = xtc_href_link('shop.php',
                'do=PayPalHub/RedirectGuest',
                'SSL',
                false,
                false,
                false,
                false,
                true);
        } else {
            $checkoutUrl = xtc_href_link('checkout_shipping.php', '', 'SSL', false, false, false, false, true);
        }
        $buttonConfig = $buttonSettings->getButtonSettings();
        $buttonConfig['createPaymentUrl'] = xtc_href_link('shop.php', 'do=PayPalHub/CreatePayment&initiator=ecs', 'SSL',
            false, false, false, false, true);
        $buttonConfig['createOrderUrl'] = xtc_href_link('shop.php', 'do=PayPalHub/CreateOrder&initiator=ecs', 'SSL',
            false, false, false, false, true);
        $buttonConfig['authorizedPaymentUrl'] = xtc_href_link('shop.php',
            'do=PayPalHub/AuthorizedPayment&initiator=ecs', 'SSL', false, false, false, false, true);
        $buttonConfig['approvedOrderUrl'] = xtc_href_link('shop.php',
            'do=PayPalHub/ApprovedOrder&initiator=ecs', 'SSL', false, false, false, false, true);
        if(isset($_SESSION['gambio_hub_payments']['PayPal2Hub']) &&
           !isset($_SESSION['PayPal2Hub']['paymentID'], $_SESSION['PayPal2Hub']['payerID'])) {
            // ECM
            $buttonConfig['createPaymentUrl']     = xtc_href_link('shop.php', 'do=PayPalHub/CreatePayment', 'SSL', false, false, false, false, true);
            $buttonConfig['authorizedPaymentUrl'] = xtc_href_link('shop.php', 'do=PayPalHub/AuthorizedPayment', 'SSL', false, false, false, false, true);
        }
        $buttonConfig['checkoutUrl']          = $checkoutUrl;
        $buttonConfig['locale'] = $this->_getPayPalLocale();
        $ecsV1Approved = isset($_SESSION['PayPal2Hub']['paymentID'], $_SESSION['PayPal2Hub']['payerID']);
        $ecsV2Approved = isset($_SESSION['PayPal2Hub']['orderID'], $_SESSION['PayPal2Hub']['payerID']);
        $buttonConfig['paymentApproved'] = $ecsV2Approved || $ecsV1Approved;
        $buttonConfig['developmentMode'] = file_exists(DIR_FS_CATALOG . '.dev-environment');
        $buttonConfig['cartAmount'] = $_SESSION['cart']->show_total();
        
        $buttonSettingsJson = json_encode($buttonConfig);
        $paramsScript .= 'var payPalButtonSettings = ' . $buttonSettingsJson . ';';
        
        if ($isBannerEnabled) {
            $bannerSettings = MainFactory::create('HubPayPalInstallmentsBannerSettings', $config);
            $bannerConfig = $bannerSettings->getInstallmentsBannerSettings();
            $bannerConfig['currency'] = $_SESSION['currency'];
            $bannerConfig['cartAmount'] = $_SESSION['cart']->show_total();
            if (isset($GLOBALS['product']) && $GLOBALS['product']->isProduct()) {
                $productsPrice      = (float)$GLOBALS['product']->data['products_price'];
                $productsTaxClassId = $GLOBALS['product']->data['products_tax_class_id'];
                $productsPrice      = $GLOBALS['xtPrice']->xtcGetPrice($GLOBALS['product']->data['products_id'],
                                                                       false,
                                                                       1,
                                                                       $productsTaxClassId,
                                                                       $productsPrice);
                $bannerConfig['productsPrice'] = $productsPrice;
            }
            $bannerSettingsJson = json_encode($bannerConfig);
            $paramsScript .= 'var payPalBannerSettings = ' . $bannerSettingsJson . ';';
        } else {
            $paramsScript .= 'var payPalBannerSettings = {};';
        }
        
        $paramsScript .= '</script>';
        
        $cookieConsentPurposeId = $this->findCookieConsentPurposeId();
        $cookieConsentDataJson = json_encode(['purpose_id' => $cookieConsentPurposeId, ]);
        $paramsScript .= '<script id="paypalconsent" type="application/json">' . $cookieConsentDataJson . '</script>';
        if ($cookieConsentPurposeId !== null) {
            $paramsScript .= <<<EOF
<script data-type="text/javascript" data-managed="as-oil" data-purposes="{$cookieConsentPurposeId}" type="as-oil">
window.PayPalAsOilClearToLoad = true;
window.dispatchEvent(new Event('PayPalClearedToLoad'));
</script>
EOF;

        }
        
        if (!is_array($this->v_output_buffer)) {
            $this->v_output_buffer = [];
        }
    
        $cacheBustingValue       = filemtime(__DIR__ . '/../../Javascript/PayPalLoader.js');
        $htaccessVersion = getenv('gambio_htaccessVersion') ?: '0.0';
        if (version_compare($htaccessVersion, '2.8', '>=')) {
            $this->v_output_buffer[] = $paramsScript
                                       . "<script src=\"GXModules/Gambio/Hub/Shop/Javascript/PayPalLoader-bust_{$cacheBustingValue}.js\" async></script>";
        } else {
            $this->v_output_buffer[] = $paramsScript
                                       . "<script src=\"GXModules/Gambio/Hub/Shop/Javascript/PayPalLoader.js?v={$cacheBustingValue}\" async></script>";
        }
    }
    
    
    protected function _getPayPalLocale()
    {
        $paypalLocaleFactory = MainFactory::create('HubPayPalLocaleFactory');
        $locale = $paypalLocaleFactory->getLocaleByLanguageAndCountry($_SESSION['language_code'], $_SESSION['delivery_zone'] ?? '');
        return $locale;
    }
    
    protected function findCookieConsentPurposeId(): ?int
    {
        if (!is_callable(['CookieConsentPanelInstallationStatus', 'create'])) {
            return null;
        }
        $ccStatus = CookieConsentPanelInstallationStatus::create();
        if (!$ccStatus->isInstalled()) {
            return null;
        }
        
        try {
            $purposeReaderService = StaticGXCoreLoader::getService('PurposeReader');
            $activePurposes = $purposeReaderService->activePurposes($_SESSION['languages_id']);

            /** @var Gambio\CookieConsentPanel\Services\Purposes\Entities\Purpose $activePurpose */
            foreach($activePurposes as $activePurpose) {
                if (stripos($activePurpose->name()->value()[$_SESSION['languages_id']], 'paypal') !== false) {
                    $payPalPurposeId = $activePurpose->id()->value();
                    return $payPalPurposeId;
                }
            }
        } catch (\Exception $e) {
            // no consent management available
            return null;
        }
    
        return null;
    }
}
