<?php
/* --------------------------------------------------------------
   FilterManager.inc.php 2017-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FilterManager
{
	public $v_categories_id = false;
	public $v_feature_value_id_array = array();

	public $v_feature_value_group_array = array();

	public $v_price_range_start = false;
	public $v_price_range_end = false;

	public $v_filter_active = false;


	public function reset()
	{
		$this->v_categories_id = false;
		$this->v_feature_value_id_array = array();
		
		$this->v_feature_value_group_array = array();

		$this->v_price_range_start = false;
		$this->v_price_range_end = false;

		$this->v_filter_active = false;
	}

	public function set_active($p_status_active)
	{
		$this->v_filter_active = (bool)$p_status_active;
	}

	public function is_active()
	{
		$t_output = (bool)$this->v_filter_active;
		return $t_output;
	}



	public function set_categories_id($p_categories_id)
	{
		$c_categories_id = (int)$p_categories_id;
		$this->v_categories_id = $c_categories_id;
	}
	// end of member function add_categories_id

	public function get_categories_id()
	{
		return $this->v_categories_id;
	}



	public function add_feature_value_group($p_feature_value_id_array, $p_conjunction)
	{
		if(isset($GLOBALS['coo_debugger']) && is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('add_feature_value_group() _GET: '. print_r($_GET['filter_fv_id'], true), 'FilterManager');

		if(isset($GLOBALS['coo_debugger']) && is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('add_feature_value_group() add: '. print_r($p_feature_value_id_array, true), 'FilterManager');

		# dont add invalid group
		if(is_array($p_feature_value_id_array) == false) return false;

		# 0=false, 1=true
		$c_conjunction = (bool)$p_conjunction;
		
		$this->v_feature_value_group_array[] = array(
													'FEATURE_VALUE_ID_ARRAY' => $p_feature_value_id_array,
													'VALUE_CONJUNCTION' => $c_conjunction
												);

		if(isset($GLOBALS['coo_debugger']) && is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('add_feature_value_group() GROUP: '. print_r($this->v_feature_value_group_array, true), 'FilterManager');

		return true;
	}

	public function get_feature_value_group_array()
	{
		return $this->v_feature_value_group_array;
	}


	/**
	* ids for AND operations
	*
	* @param int p_feature_value_id
	* @return
	* @access public
	*/
	public function add_feature_value_id($p_feature_value_id)
	{
		$c_feature_value_id = (int)$p_feature_value_id;
		
		# dont add invalid ids
		if($c_feature_value_id == 0) return false;

		$this->v_feature_value_id_array[] = $c_feature_value_id;
		return true;
	}
	// end of member function add_feature_value_id

	public function get_feature_value_id_array()
	{
		return $this->v_feature_value_id_array;
	}

	/**
	*
	*
	* @param float p_from
	* @param float p_to
	* @return
	* @access public
	*/
	public function set_price_range_start($p_price)
	{
		$t_price = str_replace(',', '.', $p_price);
		$this->v_price_range_start = (float)$t_price;
	}

	public function get_price_range_start()
	{
		return $this->v_price_range_start;
	}

	public function set_price_range_end($p_price)
	{
		$t_price = str_replace(',', '.', $p_price);
		$this->v_price_range_end = (float)$t_price;
	}

	public function get_price_range_end()
	{
		return $this->v_price_range_end;
	}
}
