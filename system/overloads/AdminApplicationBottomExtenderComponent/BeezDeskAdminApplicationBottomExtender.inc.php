<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2015 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

 */

/* --------------------------------------------------------------
   MailBeez Integration
   --------------------------------------------------------------
*/

use Gambio\Core\Configuration\ConfigurationService;

require_once(DIR_FS_INC . 'set_mailbeez_env.inc.php');
set_mailbeez_env();

class BeezDeskAdminApplicationBottomExtender extends BeezDeskAdminApplicationBottomExtender_parent
{
    function proceed()
    {
        parent::proceed();
        
        /**
         * load configuration values
         */
        
        /** @var ConfigurationService $service */
        $service                   = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $mailbeezMailhiveStatus    = null;
        $mailbeezInsightViewStatus = null;
        
        if ($service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')) {
            $mailbeezMailhiveStatusConfig = json_decode(
                $service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')->value(),
                true
            );
            $mailbeezMailhiveStatus       = $mailbeezMailhiveStatusConfig['value'];
        }
        if ($service->find('mailbeez/MAILBEEZ_INSIGHT_VIEW_STATUS')) {
            $mailbeezInsightViewStatusConfig = json_decode(
                $service->find('mailbeez/MAILBEEZ_INSIGHT_VIEW_STATUS')->value(),
                true
            );
            $mailbeezInsightViewStatus       = $mailbeezInsightViewStatusConfig['value'];
        }
        
        if (!defined('MAILBEEZ_MAILHIVE_STATUS')) {
            define('MAILBEEZ_MAILHIVE_STATUS', $mailbeezMailhiveStatus);
        }
        
        if (!defined('MAILBEEZ_INSIGHT_VIEW_STATUS')) {
            define('MAILBEEZ_INSIGHT_VIEW_STATUS', $mailbeezInsightViewStatus);
        }
        
        if (MAILBEEZ_MAILHIVE_STATUS == 'True') {
            if (MAILBEEZ_INSIGHT_VIEW_STATUS == 'True') {
                // BeezDesk
                // BOF: Mailbeez Customer Insight
                if (!defined('MH_DIR_FS_CATALOG')) {
                    define(
                        'MH_DIR_FS_CATALOG',
                        (substr(DIR_FS_CATALOG, -1) != '/') ? DIR_FS_CATALOG . '/' : DIR_FS_CATALOG
                    );
                }
                if (file_exists(
                    MH_DIR_FS_CATALOG . MH_ROOT_PATH
                    . 'configbeez/config_customer_insight/includes/admin_footer_include.php'
                )) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH
                            . 'configbeez/config_customer_insight/includes/admin_footer_include.php');
                }
                // EOF: Mailbeez Customer Insight
                // BeezDesk
            }
        }
    }
}
