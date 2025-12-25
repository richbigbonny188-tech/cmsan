<?php
/* --------------------------------------------------------------
   ot_tax.php 2023-04-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_tax.php,v 1.14 2003/02/14); www.oscommerce.com  
   (c) 2003	 nextcommerce (ot_tax.php,v 1.11 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_tax.php 1002 2005-07-10 16:11:37Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  class ot_gm_tax_free_ORIGIN {
    var $title, $output;
    
    public $code;
    public $description;
    public $enabled;
    public $sort_order;
    
    public function __construct() {
    	global $xtPrice;
      $this->code = 'ot_gm_tax_free';

      $this->title = defined('MODULE_ORDER_TOTAL_GM_TAX_FREE_TITLE') ? MODULE_ORDER_TOTAL_GM_TAX_FREE_TITLE : '';
      $this->description = defined('MODULE_ORDER_TOTAL_GM_TAX_FREE_DESCRIPTION') ? MODULE_ORDER_TOTAL_GM_TAX_FREE_DESCRIPTION : '';
      $this->enabled = defined('MODULE_ORDER_TOTAL_GM_TAX_FREE_STATUS') && MODULE_ORDER_TOTAL_GM_TAX_FREE_STATUS === 'true';
      $this->sort_order = defined('MODULE_ORDER_TOTAL_GM_TAX_FREE_SORT_ORDER') ? MODULE_ORDER_TOTAL_GM_TAX_FREE_SORT_ORDER : '0';

      $this->output = array();
    }

    function process() {


		$this->output[] = array('title' => MODULE_ORDER_TOTAL_GM_TAX_FREE_TEXT,
								'text' =>"",
								'value' => "");   
		}

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select `value` from `gx_configurations` where `key` = 'configuration/MODULE_ORDER_TOTAL_GM_TAX_FREE_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('configuration/MODULE_ORDER_TOTAL_GM_TAX_FREE_STATUS', 'configuration/MODULE_ORDER_TOTAL_GM_TAX_FREE_SORT_ORDER');
    }

    function install() {
      xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_GM_TAX_FREE_STATUS', 'true', '0', 'switcher')");
      xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_GM_TAX_FREE_SORT_ORDER', '50', '2')");
    }

    function remove() {
      xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
  }
  
MainFactory::load_origin_class('ot_gm_tax_free');