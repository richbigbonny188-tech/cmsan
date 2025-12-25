<?php
/* --------------------------------------------------------------
  cookie_consent_panel_is_installed.php 2020-01-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * @return bool
 */
function cookie_consent_panel_is_installed()
{
    static $cookieConsentPanelInstallationStatus;
    
    if ($cookieConsentPanelInstallationStatus === null) {
    
        $cookieConsentPanelInstallationStatus = CookieConsentPanelInstallationStatus::create();
    }
    
    return $cookieConsentPanelInstallationStatus->isInstalled();
}