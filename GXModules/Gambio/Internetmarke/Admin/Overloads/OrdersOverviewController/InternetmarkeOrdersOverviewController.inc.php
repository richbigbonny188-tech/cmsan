<?php
/* --------------------------------------------------------------
	InternetmarkeOrdersOverviewController.inc.php 2018-05-22
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class InternetmarkeOrdersOverviewController extends InternetmarkeOrdersOverviewController_parent
{
	protected function _getAssetsArray()
	{
		$assets = parent::_getAssetsArray();
		if(gm_get_conf('MODULE_CENTER_INTERNETMARKE_INSTALLED') == true)
		{
			$assets[] = MainFactory::create('Asset', 'internetmarke.lang.inc.php');
			$assets[] = MainFactory::create('Asset', DIR_WS_CATALOG
			                                         . 'GXModules/Gambio/Internetmarke/Build/Admin/Javascript/modules/internetmarke/internetmarke.js');
			$assets[] = MainFactory::create('Asset', DIR_WS_CATALOG . 'GXModules/Gambio/Internetmarke/Admin/Styles/internetmarke.css');
		}

		return $assets;
	}
}
