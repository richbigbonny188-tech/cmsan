<?php
/* --------------------------------------------------------------
	InternetMarkeAdminOrderOverviewExtenderComponent.inc.php 2018-04-05
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class InternetMarkeAdminOrderOverviewExtenderComponent extends InternetMarkeAdminOrderOverviewExtenderComponent_parent
{
	function proceed()
	{
		parent::proceed();
		if(gm_get_conf('MODULE_CENTER_INTERNETMARKE_INSTALLED') == true)
		{
			$GLOBALS['jsEngineLanguage']['internetmarke'] = $GLOBALS['languageTextManager']->get_section_array('internetmarke');
			$this->v_output_buffer['single_action'] = '<a class="inetmarke_single" href="internetmarke_dummy.php?oID=0" data-gx-compatibility="orders/orders_internetmarke">'.$GLOBALS['jsEngineLanguage']['internetmarke']['create_label'].'</a>';
		}
	}
}
