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
use Gambio\Core\Configuration\ConfigurationService;


class MailBeezShopvotingBoxThemeContentView extends ThemeContentView
{
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('box_mailbeez_shopvoting.html');
        $this->set_caching_enabled(false);
        $this->build_html = false;
    }

    public function prepare_data()
    {
        if ($this->isWidgetActive()) {
            if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting_widget.php')) {
                require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting_widget.php');
                $shopvoting = new Shopvoting_widget();
                $this->content_array['WIDGET_CODE'] = $shopvoting->output();
            }
        } elseif (StyleEditServiceFactory::service()->isEditing()) {
            $this->build_html = true;
            $this->content_array['WIDGET_CODE'] = 'MailBeez Shopvoting Widget Dummytext';
        }
    }

    protected function isWidgetActive()
    {

        /** @var ConfigurationService $service */
        $service                  = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $mailbeezMailhiveStatus   = null;
        $mailbeezShopvotingStatus = null;

        if ($service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')) {
            $mailbeezMailhiveStatusConfig = json_decode($service->find('mailbeez/MAILBEEZ_MAILHIVE_STATUS')->value(),
                                                        true);
            $mailbeezMailhiveStatus       = $mailbeezMailhiveStatusConfig['value'];
        }
        if ($service->find('mailbeez/MAILBEEZ_SHOPVOTING_STATUS')) {
            $mailbeezShopvotingStatusConfig =
                json_decode($service->find('mailbeez/MAILBEEZ_SHOPVOTING_STATUS')->value(),
                            true);
            $mailbeezShopvotingStatus       = $mailbeezShopvotingStatusConfig['value'];
        }
        
        if (!defined('MAILBEEZ_MAILHIVE_STATUS')) {
            define('MAILBEEZ_MAILHIVE_STATUS', $mailbeezMailhiveStatus);
        }
        
        if (!defined('MAILBEEZ_SHOPVOTING_STATUS')) {
            define('MAILBEEZ_SHOPVOTING_STATUS', $mailbeezShopvotingStatus);
        }

        if (MAILBEEZ_MAILHIVE_STATUS == 'True' && MAILBEEZ_SHOPVOTING_STATUS == 'True') {

            if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting.php')) {
                require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting.php');
            }

            $voting          = new Shopvoting();
            $readAccessArray = explode(',', $voting->customer_group_read);

            if (in_array($_SESSION['customers_status']['customers_status_id'], $readAccessArray)) {
                $this->build_html = true;
            }
        }

        return $this->build_html;
    }
}
