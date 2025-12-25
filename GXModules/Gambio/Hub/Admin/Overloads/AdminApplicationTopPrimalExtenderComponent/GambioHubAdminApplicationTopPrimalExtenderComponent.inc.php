<?php
/* --------------------------------------------------------------
   GambioHubAdminApplicationTopPrimalExtenderComponent.inc.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubAdminApplicationTopPrimalExtenderComponent
	extends GambioHubAdminApplicationTopPrimalExtenderComponent_parent
{
	public function proceed()
	{
		parent::proceed();
		
		HubAutoloader::register();
		
		if(class_exists('DataObserverRegistry'))
		{
			DataObserverRegistry::activate();
		}
	}
}
