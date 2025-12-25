<?php
/* --------------------------------------------------------------
  JSSectionExtenderComponent.inc.php 2022-05-04 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class JSSectionExtenderComponent extends ExtenderComponent
{
	function get_permission_status($p_customers_id = NULL)
	{
		return true;
	}
	
	function proceed() 
	{
		parent::proceed();
		
		if(method_exists($this, $this->v_data_array['GET']['section']))
		{
			echo '$(function(){';
            
            $lightboxIdKeyExists = array_key_exists('GET', $this->v_data_array) && is_array($this->v_data_array['GET'])
                                   && array_key_exists('lightbox_identifier', $this->v_data_array['GET']);
            
			if($lightboxIdKeyExists && $this->v_data_array['GET']['lightbox_identifier'] )
			{
				echo 'var t_lightbox_identifier = "' . rawurlencode($this->v_data_array['GET']['lightbox_identifier']) . '";';
				echo 'var t_lightbox_package = $( "#lightbox_package_" + t_lightbox_identifier );';
				echo 'var t_lightbox_data = t_lightbox_parameters[ "_' . rawurlencode($this->v_data_array['GET']['lightbox_identifier']) . '" ];' . "\n\n\n";
			}

			call_user_func(array($this,$this->v_data_array['GET']['section']));
			
			echo '});';
		}
	}
}