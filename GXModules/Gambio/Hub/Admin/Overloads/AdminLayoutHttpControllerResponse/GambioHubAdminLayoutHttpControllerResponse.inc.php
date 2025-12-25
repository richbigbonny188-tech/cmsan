<?php
/* --------------------------------------------------------------
   GambioHubAdminLayoutHttpControllerResponse.inc.php 2017-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubAdminLayoutHttpControllerResponse
 *
 * Includes the footer JS extender that indicates the Gambio Hub connection status.
 */
class GambioHubAdminLayoutHttpControllerResponse extends GambioHubAdminLayoutHttpControllerResponse_parent
{
	/**
	 * Adds the footer JS extender script into the page.
	 *
	 * Important: We overload the "_setInitialMessages" method in order to add the required JS file, cause
	 * this method is executed after the "dynamic_script_assets" content data are set (the timing suits for this task).
	 */
	protected function _setInitialMessages()
	{
		$languageTextManager = MainFactory::create('LanguageTextManager', 'admin_general', $_SESSION['languages_id']);
		$installedVersion = gm_get_conf('INSTALLED_VERSION');
		$hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);
		
		$connected   = (bool)gm_get_conf('GAMBIO_HUB_CLIENT_KEY') ? 'data-connected' : '';
		$translation = $connected ? $languageTextManager->get_text('TEXT_HUB_CONNECTED') : $languageTextManager->get_text('TEXT_HUB_DISCONNECTED');
		$text        = 'data-text="' . $translation . '"';
		
		$postfix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
		$src     = DIR_WS_CATALOG . $hubAssetHelper->getScriptsBaseUrl() . '/extenders/footer_hub_state' . $postfix
		           . '.js';
		
		$contentData = $this->contentView->get_content_array();
		
		$scripts = $contentData['dynamic_script_assets'] . PHP_EOL . '<script src="' . $src . '" ' . $connected . ' '
		           . $text . '></script>';
		$this->contentView->set_content_data('dynamic_script_assets', $scripts);
		
		return parent::_setInitialMessages();
	}
}