<?php
/* --------------------------------------------------------------
   SSOAccountThemeContentView.inc.php 2023-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\CookieConsentPanel\Services\Purposes\Factories\PurposeReaderServiceFactory;

class SSOAccountThemeContentView extends SSOAccountThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        
        $moduleInstalled = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        if ($moduleInstalled === true
            && $_SESSION['customers_status']['customers_status_id'] !== DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
            $db               = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $customerSSOQuery = $db->get_where('customers_sso', ['customers_id' => $_SESSION['customer_id']]);
            $connectedIssuers = [];
            
            foreach ($customerSSOQuery->result_array() as $ssoRow) {
                $connectedIssuers[] = $ssoRow['issuer'];
            }
            
            $ssoButtons       = [];
            $ssoConfiguration = MainFactory::create('SingleSignonConfigurationStorage');
            $loginUrl         = xtc_href_link('shop.php',
                                              'do=SingleSignOn/Redirect',
                                              'SSL',
                                              false,
                                              false,
                                              false,
                                              true,
                                              true);
            if ((bool)$ssoConfiguration->get('services/google/active') === true) {
                $googleLoginUrl       = $loginUrl . '&amp;service=google';
                $ssoButtons['google'] = [
                    'loginUrl'  => $googleLoginUrl,
                    'connected' => in_array('https://accounts.google.com', $connectedIssuers, true),
                    'issuer'    => 'google',
                ];
            }
            if ((bool)$ssoConfiguration->get('services/facebook/active') === true) {
                $facebookLoginUrl       = $loginUrl . '&amp;service=facebook';
                $ssoButtons['facebook'] = [
                    'loginUrl'  => $facebookLoginUrl,
                    'connected' => in_array('facebook.com', $connectedIssuers, true),
                    'issuer'    => 'facebook',
                ];
            }
            if ((bool)$ssoConfiguration->get('services/paypal/active') === true) {
                $paypalLoginUrl       = $loginUrl . '&amp;service=paypal';
                $ssoButtons['paypal'] = [
                    'loginUrl'  => $paypalLoginUrl,
                    'connected' => in_array('paypal.com', $connectedIssuers, true),
                    'issuer'    => 'paypal',
                ];
            }
            if ((bool)$ssoConfiguration->get('services/amazon/active') === true) {
                $amazonLoginUrl       = $loginUrl . '&amp;service=amazon';
                $ssoButtons['amazon'] = [
                    'loginUrl'  => $amazonLoginUrl,
                    'connected' => in_array('amazon.com', $connectedIssuers, true),
                    'issuer'    => 'amazon',
                ];
            }
            
            $ssoData = [
                'issuers'                  => $connectedIssuers,
                'ssoButtons'               => $ssoButtons,
                'delete_connection_action' => xtc_href_link('shop.php', 'do=SingleSignOn/DeleteSsoConnection'),
            ];
            
            $this->set_content_data('ssoData', $ssoData);
            
            $cookieConsentIsInstalled = cookie_consent_panel_is_installed();
            if ($cookieConsentIsInstalled) {
                $purposeId       = $this->findPurposeIdByAlias();
                $purposeIsActive = cookie_purpose_is_active($purposeId);
                
                $this->set_content_data('ssoCookieConsentPurposeId', $purposeId);
                $this->set_content_data('ssoCookieConsentPurposeIsActive', $purposeIsActive);
                $this->set_content_data('cookieConsentIsInstalled', $cookieConsentIsInstalled);
            }
        }
    }
    
    
    /**
     * @param string $serviceName
     *
     * @return bool
     */
    protected function isServiceActive(string $serviceName): bool
    {
        $isActive = false;
        
        switch ($serviceName) {
            case 'google':
                $isActive = CookiesConsentSsoStore::google() !== null && CookiesConsentSsoStore::google()->isActive();
                break;
            case 'facebook':
                $isActive = CookiesConsentSsoStore::facebook() !== null
                            && CookiesConsentSsoStore::facebook()->isActive();
                break;
            case 'paypal':
                $isActive = CookiesConsentSsoStore::payPal() !== null && CookiesConsentSsoStore::payPal()->isActive();
                break;
            case 'amazon':
                $isActive = CookiesConsentSsoStore::amazon() !== null && CookiesConsentSsoStore::amazon()->isActive();
                break;
        }
        
        return $isActive;
    }
    
    
    /**
     * @return int|null
     */
    protected function findPurposeIdByAlias(): ?int
    {
        $purposeReaderServiceFactory = new PurposeReaderServiceFactory();
        $service                     = $purposeReaderServiceFactory->service();
        $purposes                    = $service->allPurposes();
        
        $ssoPurpose = array_filter($purposes,
            static fn($purpose) => $purpose->alias()->value() === SingleSignonModuleCenterModule::COOKIE_CONSENT_SSO_ALIAS);
        
        $ssoPurpose = reset($ssoPurpose);
        
        return $ssoPurpose->id()->value() ?? null;
    }
}
