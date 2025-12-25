<?php
/* --------------------------------------------------------------
   InternetmarkeOrderExtenderComponent.inc.php 2018-04-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class InternetmarkeOrderExtenderComponent extends InternetmarkeOrderExtenderComponent_parent
{
	public function proceed()
	{
		parent::proceed();
		if((bool)gm_get_conf('MODULE_CENTER_INTERNETMARKE_INSTALLED') === true) {
			$scriptTag = sprintf(
					'<div class="internetmarke_orderdetails" ' .
							'data-gxmodules-namespace="%s/GXModules/Gambio/Internetmarke/Build/Admin/Javascript" ' .
							'data-gxmodules-controller="compatibility/orders_internetmarke_new"></div>',
						GM_HTTP_SERVER . DIR_WS_CATALOG
			);
			$this->addContentToCollection('below_history', $scriptTag, '');
		}
	}
}
