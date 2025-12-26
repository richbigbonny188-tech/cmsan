<?php
/* --------------------------------------------------------------
   GambioHubAdminMenuContentView.inc.php 2017-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubAdminMenuContentView
 */
class GambioHubAdminMenuContentView extends GambioHubAdminMenuContentView_parent
{
	/**
	 * Adds footer JS extender script for Gambio Hub connectivity state.
	 */
	public function get_html()
	{
		$html = parent::get_html();
		
		$languageTextManager = MainFactory::create('LanguageTextManager', 'admin_general', $_SESSION['languages_id']);
		$installedVersion = gm_get_conf('INSTALLED_VERSION');
		$hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);
		
		$connected   = (bool)gm_get_conf('GAMBIO_HUB_CLIENT_KEY') ? 'data-connected' : '';
		$translation = $connected ? $languageTextManager->get_text('TEXT_HUB_CONNECTED') : $languageTextManager->get_text('TEXT_HUB_DISCONNECTED');
		$text        = 'data-text="' . $translation . '"';
		
		$postfix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
		$src     = DIR_WS_CATALOG . $hubAssetHelper->getScriptsBaseUrl() .'/extenders/footer_hub_state' . $postfix
		           . '.js';
		
		$html .= PHP_EOL . '<script src="' . $src . '" ' . $connected . ' ' . $text . ' data-legacy-mode></script>';
		
		return $html;
	}
}