<?php
/* --------------------------------------------------------------
   ShopContentAjaxHandler.inc.php 2022-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShopContentAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return $this->_checkAdminReadingPermission('ShopContent', !empty($p_customers_id) ? $p_customers_id : $_SESSION['customer_id']);
		}
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = false;

		$t_action_request = $this->v_data_array['GET']['action'];

		switch($t_action_request)
		{	
			case 'download':
				$coo_shop_content_control = MainFactory::create_object('ShopContentContentControl');
				
				if(isset($this->v_data_array['GET']['coID']))
				{
					$coo_shop_content_control->set_content_group($this->v_data_array['GET']['coID']);
				}
				
				if(isset($this->v_data_array['GET']['customer_status_id']) && (int)$this->v_data_array['GET']['customer_status_id'] > 0)
				{
					$coo_shop_content_control->set_customer_status_id((int)$this->v_data_array['GET']['customer_status_id']);
				}
				
				if(isset($this->v_data_array['GET']['withdrawal_form']) && (int)$this->v_data_array['GET']['withdrawal_form'] > 0)
				{
					$coo_shop_content_control->set_withdrawal_form((int)$this->v_data_array['GET']['withdrawal_form']);
				}
				
				$t_file = $coo_shop_content_control->get_file();
				xtc_db_close();
                
                if(is_string($t_file['path']) === false || file_exists($t_file['path']) === false){
                    if (!headers_sent()) {
                        Gambio\GX\Application::send404HttpResponse();
                    }
                    exit(0);
                }
				
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . $t_file['name'] . '"');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				readfile($t_file['path']);
				exit(0);
				break;
				
			default:
                if (file_exists(DIR_FS_CATALOG . '.dev-environment')) {
                    trigger_error('t_action_request not found: ' . htmlentities($t_action_request), E_USER_WARNING);
                }
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = MainFactory::create_object('GMJSON', array(false));
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}
		return true;
	}
}
