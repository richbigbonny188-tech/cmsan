<?php
/* --------------------------------------------------------------
   function.object_product_list.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_load_language_text($params, &$smarty)
{
    $t_use_fallback = true;
    if (isset($params['use_fallback'])) {
        $t_use_fallback = ($params['use_fallback'] == 'false') === false;
    }
    
    # get existing instance (performance)
    $t_use_singleton = true;
    if (isset($params['use_singleton'])) {
        $t_use_singleton = $params['use_singleton'] != 'false';
    }
    
    # select section
    $t_section = [];
    if (isset($params['section'])) {
        $t_section = $params['section'];
    }
    
    # set array name
    $_text_array_name = 'txt';
    if (isset($params['name'])) {
        $_text_array_name = $params['name'];
    }
    
    # select language
    $t_language_id = $_SESSION['languages_id'];
    
    $coo_text_mgr = MainFactory::create_object('LanguageTextManager',
                                               [$t_section, $t_language_id, $t_use_fallback],
                                               $t_use_singleton);
    
    $t_section_array = $coo_text_mgr->get_section_array();
    
    if ($_text_array_name === 'txt' && isset($smarty->tpl_vars[$_text_array_name]->value)
        && is_array($smarty->tpl_vars[$_text_array_name]->value)) {
        $t_section_array = array_merge($smarty->tpl_vars[$_text_array_name]->value, $t_section_array);
    }
    
    $smarty->assign($_text_array_name, $t_section_array);
}

/* vim: set expandtab: */

?>