<?php
/* --------------------------------------------------------------
   SSOLoginContentView.inc.php 2023-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\CookieConsentPanel\Services\Purposes\Factories\PurposeReaderServiceFactory;

class SSOLoginThemeContentView extends SSOLoginThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        
        $ssoData         = [];
        $moduleInstalled = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        if ($moduleInstalled === true) {
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
                $ssoData['googleLoginUrl'] = $loginUrl . '&amp;service=google';
            }
            if ((bool)$ssoConfiguration->get('services/facebook/active') === true) {
                $ssoData['facebookLoginUrl'] = $loginUrl . '&amp;service=facebook';
            }
            if ((bool)$ssoConfiguration->get('services/paypal/active') === true) {
                $ssoData['paypalLoginUrl'] = $loginUrl . '&amp;service=paypal';
            }
            if ((bool)$ssoConfiguration->get('services/amazon/active') === true) {
                $ssoData['amazonLoginUrl'] = $loginUrl . '&amp;service=amazon';
            }
        }
        $this->set_content_data('ssoData', $ssoData);
        
        $cookieConsentIsInstalled = cookie_consent_panel_is_installed();
        if ($cookieConsentIsInstalled) {
            $purposeId = $this->findPurposeIdByAlias();
            
            if ($purposeId === null) {
                return;
            }
            
            $purposeIsActive = cookie_purpose_is_active($purposeId);
            
            $this->set_content_data('ssoCookieConsentPurposeId', $purposeId);
            $this->set_content_data('ssoCookieConsentPurposeIsActive', $purposeIsActive);
            $this->set_content_data('cookieConsentIsInstalled', $cookieConsentIsInstalled);
        }
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
        
        if ($ssoPurpose === false) {
            return null;
        }
        
        return $ssoPurpose->id()->value() ?? null;
    }
}
