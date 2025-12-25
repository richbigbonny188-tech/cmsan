<?PHP
/* -----------------------------------------------------------------------------------------
   $Id: selfpickup.php 1306 2005-10-14 10:32:31Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(freeamount.php,v 1.01 2002/01/24); www.oscommerce.com 
   (c) 2003	 nextcommerce (freeamount.php,v 1.12 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   selfpickup         	Autor:	sebthom

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class selfpickup_ORIGIN
{
    var $code, $title, $description, $icon, $enabled;
    
    
    public function __construct()
    {
        $this->code        = 'selfpickup';
        $this->title       = defined('MODULE_SHIPPING_SELFPICKUP_TEXT_TITLE') ? MODULE_SHIPPING_SELFPICKUP_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_SELFPICKUP_TEXT_DESCRIPTION') ? MODULE_SHIPPING_SELFPICKUP_TEXT_DESCRIPTION : '';
        $this->icon        = '';   // change $this->icon =  DIR_WS_ICONS . 'shipping_ups.gif'; to some freeshipping icon
        $this->sort_order  = defined('MODULE_SHIPPING_SELFPICKUP_SORT_ORDER') ? MODULE_SHIPPING_SELFPICKUP_SORT_ORDER : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
    }
    
    
    function quote($method = '')
    {
        $this->quotes = [
            'id'     => $this->code,
            'module' => MODULE_SHIPPING_SELFPICKUP_TEXT_TITLE
        ];
        
        $this->quotes['methods'] = [
            [
                'id'    => $this->code,
                'title' => MODULE_SHIPPING_SELFPICKUP_TEXT_WAY,
                'cost'  => 0
            ]
        ];
        
        if (xtc_not_null($this->icon)) {
            $this->quotes['icon'] = xtc_image($this->icon, $this->title);
        }
        
        return $this->quotes;
    }
    
    
    function check()
    {
        $check = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_SELFPICKUP_STATUS'");
        $check = xtc_db_num_rows($check);
        
        return $check;
    }
    
    
    function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_SELFPICKUP_STATUS', 'True', '7', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_SELFPICKUP_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_SELFPICKUP_SORT_ORDER', '0', '4', now())");
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        return [
            'configuration/MODULE_SHIPPING_SELFPICKUP_STATUS',
            'configuration/MODULE_SHIPPING_SELFPICKUP_SORT_ORDER',
            'configuration/MODULE_SHIPPING_SELFPICKUP_ALLOWED'
        ];
    }
}

MainFactory::load_origin_class('selfpickup');
