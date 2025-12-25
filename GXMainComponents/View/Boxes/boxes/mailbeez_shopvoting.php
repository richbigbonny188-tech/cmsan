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

/** @var ConfigurationService $service */
$service                  = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
$mailbeezShopvotingStatus = null;

if ($service->find('mailbeez/MAILBEEZ_SHOPVOTING_STATUS')) {
    $mailbeezShopvotingStatusConfig = json_decode($service->find('mailbeez/MAILBEEZ_SHOPVOTING_STATUS')->value(),
                                                  true);
    $mailbeezShopvotingStatus       = $mailbeezShopvotingStatusConfig['value'];
}

if (!defined('MAILBEEZ_SHOPVOTING_STATUS')) {
    define('MAILBEEZ_SHOPVOTING_STATUS', $mailbeezShopvotingStatus);
}

if (MAILBEEZ_SHOPVOTING_STATUS == 'True') {

    $coo_mailbeez_shopvoting = MainFactory::create_object('MailBeezShopvotingBoxThemeContentView');
    $t_box_html              = $coo_mailbeez_shopvoting->get_html();

    $gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('mailbeez_shopvoting');
    $this->set_content_data($gm_box_pos, $t_box_html);
}
