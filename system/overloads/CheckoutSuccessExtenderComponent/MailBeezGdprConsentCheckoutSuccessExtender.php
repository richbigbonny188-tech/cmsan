<?php

/*
 * This Extender adds the option to give GDPR consents
 *
 */

use Gambio\Core\Configuration\ConfigurationService;

class MailBeezGdprConsentCheckoutSuccessExtender extends MailBeezGdprConsentCheckoutSuccessExtender_parent
{
    /**
     * Overloaded "proceed" method.
     */
    public function proceed()
    {
        parent::proceed();
        
        /**
         * load configuration values
         */
        
        /** @var ConfigurationService $service */
        $service                   = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $mailbeezMailhiveStatus    = null;
        $mailbeezConfigGdprConsent = null;
        
        if ($service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')) {
            $mailbeezMailhiveStatusConfig = json_decode(
                $service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')->value(),
                true
            );
            $mailbeezMailhiveStatus       = $mailbeezMailhiveStatusConfig['value'];
        }
        if ($service->find('mailbeez/MAILBEEZ_CONFIG_GDPR_CONSENT_STATUS')) {
            $mailbeezConfigGdprConsentConfig = json_decode(
                $service->find('mailbeez/MAILBEEZ_CONFIG_GDPR_CONSENT_STATUS')->value(),
                true
            );
            $mailbeezConfigGdprConsent       = $mailbeezConfigGdprConsentConfig['value'];
        }

        if (!defined('MAILBEEZ_MAILHIVE_STATUS')) {
            define('MAILBEEZ_MAILHIVE_STATUS', $mailbeezMailhiveStatus);
        }
        
        if (!defined('MAILBEEZ_CONFIG_GDPR_CONSENT_STATUS')) {
            define('MAILBEEZ_CONFIG_GDPR_CONSENT_STATUS', $mailbeezConfigGdprConsent);
        }
        
        if (MAILBEEZ_MAILHIVE_STATUS == 'True' && MAILBEEZ_CONFIG_GDPR_CONSENT_STATUS == 'True') {
            if (file_exists(
                MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_gdpr_consent/includes/inc.gdpr_consent.php'
            )) {
                include_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH
                             . 'configbeez/config_gdpr_consent/includes/inc.gdpr_consent.php');
                
                $customer_id   = $this->v_data_array['coo_order']->customer['id'];
                $email_address = $this->v_data_array['coo_order']->customer['email_address'];
                
                $gdpr                      = new mh_gdpr_render_integration();
                $output                    = $gdpr->render(
                    [
                        'customer_id'   => $customer_id,
                        'email_address' => $email_address,
                        'template'      => 'gambio_checkoutsuccess',
                        'mode'          => 'slim'
                    ]
                );
                $this->html_output_array[] = $output;
            }
        }
    }
    
}