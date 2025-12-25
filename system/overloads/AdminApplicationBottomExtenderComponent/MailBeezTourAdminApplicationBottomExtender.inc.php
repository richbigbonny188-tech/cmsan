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

use Gambio\Core\Configuration\Builder\ConfigurationFinderBuilder;

class MailBeezTourAdminApplicationBottomExtender extends MailBeezTourAdminApplicationBottomExtender_parent
{
    function proceed()
    {
        parent::proceed();
        
        /**
         * load configuration values
         */
        
        /** @var ConfigurationFinderBuilder $builder */
        $builder = LegacyDependencyContainer::getInstance()->get(ConfigurationFinderBuilder::class);
        $service = $builder->buildNamespaceFinder('mailbeez');
        
        $mailbeezMailhiveStatus = null;
        $mailbeezTourStatus = null;
        
        if ($mailhiveStatus = $service->get('MAILBEEZ_MAILHIVE_STATUS')) {
            $mailbeezMailhiveStatusConfig = json_decode($mailhiveStatus, true);
            $mailbeezMailhiveStatus       = $mailbeezMailhiveStatusConfig['value'];
        }
        
        if ($tourStatus = $service->get('MAILBEEZ_TOUR_STATUS')) {
            $mailbeezTourStatusConfig = json_decode($tourStatus, true);
            $mailbeezTourStatus       = $mailbeezTourStatusConfig['value'];
        }

        if (!defined('MAILBEEZ_MAILHIVE_STATUS')) {
            define('MAILBEEZ_MAILHIVE_STATUS', $mailbeezMailhiveStatus);
        }
        
        if (!defined('MAILBEEZ_TOUR_STATUS')) {
            define('MAILBEEZ_TOUR_STATUS', $mailbeezTourStatus);
        }
        
        if (MAILBEEZ_MAILHIVE_STATUS == 'True') {
            if (MAILBEEZ_TOUR_STATUS == 'True') {
                // MailBeez tour
                if (!defined('MH_DIR_FS_CATALOG')) {
                    define(
                        'MH_DIR_FS_CATALOG',
                        (substr(DIR_FS_CATALOG, -1) != '/') ? DIR_FS_CATALOG . '/' : DIR_FS_CATALOG
                    );
                }
                if (file_exists(
                    MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_tour/includes/inc_config_tour.php'
                )) {
                    include(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_tour/includes/inc_config_tour.php');
                }
                // MailBeez tour
            }
        }
    }
}
