<?php
/* --------------------------------------------------------------
   LoadNewsAjaxHandler.inc.php 2018-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LoadNewsAjaxHandler extends AjaxHandler
{
	public function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return $this->_checkAdminReadingPermission('LoadNews', !empty($p_customers_id) ? $p_customers_id : $_SESSION['customer_id']);
		}
		return false;
	}

	public function proceed()
	{
		$adminFeedFacade = MainFactory::create(AdminFeedFacade::class);
		$this->v_output_buffer = $adminFeedFacade->adminNews();

		return true;
	}
}