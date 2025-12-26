<?php
/* --------------------------------------------------------------
   GambioHubApplicationTopPrimalExtenderComponent.inc.php 2017-06-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubApplicationTopPrimalExtenderComponent extends GambioHubApplicationTopPrimalExtenderComponent_parent
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
