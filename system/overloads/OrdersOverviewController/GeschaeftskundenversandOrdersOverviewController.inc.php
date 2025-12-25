<?php
/* --------------------------------------------------------------
	GeschaeftskundenversandOrdersOverviewController.inc.php 2016-07-06
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class GeschaeftskundenversandOrdersOverviewController extends GeschaeftskundenversandOrdersOverviewController_parent
{
	protected function _getAssetsArray()
	{
		$assets = parent::_getAssetsArray();
		if(gm_get_conf('MODULE_CENTER_GESCHAEFTSKUNDENVERSAND_INSTALLED') == true)
		{
			$assets[] = MainFactory::create('Asset', 'module_center_module.lang.inc.php');
			$assets[] = MainFactory::create('Asset', DIR_WS_CATALOG
			                                         . 'admin/html/assets/javascript/modules/geschaeftskundenversand/geschaeftskundenversand.min.js');
		}
		return $assets;
	}
}
