<?php

/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2015 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

use Gambio\Core\Configuration\ConfigurationService;

class MailBeezApplicationBottomExtender extends MailBeezApplicationBottomExtender_parent
{
    function proceed()
    {
        
        /**
         * load configuration values
         */
        
        /** @var ConfigurationService $service */
        $service                    = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $mailbeezMailhiveStatus     = null;
        $mailbeezCronSimpleStatus   = null;
        $mailbeezCronAdvancedStatus = null;
        
        if ($service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')) {
            $mailbeezMailhiveStatusConfig = json_decode(
                $service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')->value(),
                true
            );
            $mailbeezMailhiveStatus       = $mailbeezMailhiveStatusConfig['value'];
        }
        if ($service->find('mailbeez/MAILBEEZ_CRON_SIMPLE_STATUS')) {
            $mailbeezCronSimpleStatusConfig = json_decode(
                $service->find('mailbeez/MAILBEEZ_CRON_SIMPLE_STATUS')->value(),
                true
            );
            $mailbeezCronSimpleStatus       = $mailbeezCronSimpleStatusConfig['value'];
        }
        if ($service->find('mailbeez/MAILBEEZ_CRON_ADVANCED_STATUS')) {
            $mailbeezCronAdvancedStatusConfig = json_decode(
                $service->find('mailbeez/MAILBEEZ_CRON_ADVANCED_STATUS')->value(),
                true
            );
            $mailbeezCronAdvancedStatus       = $mailbeezCronAdvancedStatusConfig['value'];
        }

        if (!defined('MAILBEEZ_MAILHIVE_STATUS')) {
            define('MAILBEEZ_MAILHIVE_STATUS', $mailbeezMailhiveStatus);
        }
        
        if (!defined('MAILBEEZ_CRON_SIMPLE_STATUS')) {
            define('MAILBEEZ_CRON_SIMPLE_STATUS', $mailbeezCronSimpleStatus);
        }
        
        if (!defined('MAILBEEZ_CRON_ADVANCED_STATUS')) {
            define('MAILBEEZ_CRON_ADVANCED_STATUS', $mailbeezCronAdvancedStatus);
        }
        
        if (MAILBEEZ_MAILHIVE_STATUS == 'True') {
            if (!defined('MH_DIR_FS_CATALOG')) {
                define('MH_DIR_FS_CATALOG', (substr(DIR_FS_CATALOG, -1) != '/') ? DIR_FS_CATALOG . '/' : DIR_FS_CATALOG);
            }
            
            ob_start();
            // MailBeez
            if (MAILBEEZ_CRON_SIMPLE_STATUS == 'True') {
                if (file_exists(
                    MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_cron_simple/includes/cron_simple_inc.php'
                )) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH
                            . 'configbeez/config_cron_simple/includes/cron_simple_inc.php');
                }
            }
            if (MAILBEEZ_CRON_ADVANCED_STATUS == 'True') {
                if (file_exists(
                    MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_cron_advanced/includes/cron_advanced_inc.php'
                )) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH
                            . 'configbeez/config_cron_advanced/includes/cron_advanced_inc.php');
                }
            }
            // - MailBeez
            
            // MailBeez BigData Tracking
            if (file_exists(DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_ezako/includes/eztracker.php')) {
                include(DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_ezako/includes/eztracker.php');
            }
            // MailBeez BigData Tracking
            
            $this->v_output_buffer['MAILBEEZ_BOTTOM_CODE'] = ob_get_contents();
            ob_end_clean();
        }
        
        parent::proceed();
    }
}
