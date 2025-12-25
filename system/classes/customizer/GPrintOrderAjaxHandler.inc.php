<?php
/* --------------------------------------------------------------
   GPrintOrderAjaxHandler.inc.php 2017-12-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/GMJSON.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintFileManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintSurfacesManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintSurfaces.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintElements.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintSurfacesGroupsManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintCartManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintWishlistManager.php');
require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');

class GPrintOrderAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return $this->_checkAdminReadingPermission('GPrintOrder', !empty($p_customers_id) ? $p_customers_id : $_SESSION['customer_id']);
		}
		return false;
	}

	function proceed()
	{
		$t_output = '';
		
		if($this->v_data_array['GET']['action'] == 'load_surfaces_group')
		{
			$c_surfaces_groups_id = 0;
			if(isset($this->v_data_array['GET']['surfaces_groups_id']))
			{
				$c_surfaces_groups_id = (int)$this->v_data_array['GET']['surfaces_groups_id'];
			}
			
			$coo_gprint_order_surfaces_manager = new GMGPrintOrderSurfacesManager($c_surfaces_groups_id);
			$t_output = $coo_gprint_order_surfaces_manager->load_surfaces_group($c_surfaces_groups_id);
		}
		elseif($this->v_data_array['GET']['action'] == 'save_surfaces_group_inputs')
		{
			$logger = LogControl::get_instance();
			$logger->notice('Start GXCustomizer Update AJAX');
			if(isset($this->v_data_array['POST']['surface_id']) && (int)$this->v_data_array['POST']['surface_id'] > 0)
			{
				$logger->notice('Surface ID: '.(int)$this->v_data_array['POST']['surface_id']);
				$gprintOrderSurfaces = new GMGPrintOrderSurfaces((int)$this->v_data_array['POST']['surface_id']);
				$gprintOrderSurfaces->load_elements((int)$this->v_data_array['POST']['surface_id']);
				foreach($this->v_data_array['POST']['inputs'] AS $inputs)
				{
					$logger->notice('Element ID: '.(int)$inputs['id']);
					if(isset($inputs['value']) && isset($inputs['id']) && (int)$inputs['id'] > 0)
					{
						$logger->notice('Element Value: '.xtc_db_input($inputs['value']));
						$gprintOrderElement = $gprintOrderSurfaces->get_element((int)$inputs['id']);
						if($gprintOrderElement instanceof GMGPrintOrderElements)
						{
							$gprintOrderElement->set_element_value(xtc_db_input($inputs['value']));
						}
					}
				}
			}
		}
		
		$this->v_output_buffer = $t_output;
	}
}