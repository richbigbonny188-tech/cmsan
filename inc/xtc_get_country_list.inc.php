<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_get_country_list.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_get_country_list.inc.php,v 1.5 2003/08/20); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// include needed functions
include_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
include_once(DIR_FS_INC . 'xtc_get_countries.inc.php');

function xtc_get_country_list($name, $selected = '', $parameters = '')
{
    $countriesResult = xtc_get_countriesList('');
    
    $countryList = array();
    
    foreach ($countriesResult as $country) {
        $countryList[] = array('id' => $country['countries_id'], 'text' => $country['countries_name']);
    }
    
    if (is_array($name)) {
        return xtc_draw_pull_down_menuNote($name, $countryList, $selected, $parameters);
    }
    
    return xtc_draw_pull_down_menu($name, $countryList, $selected, $parameters);
}