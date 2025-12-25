<?php
/* --------------------------------------------------------------
   function.page_id.php 2021-11-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_page_id($params, &$smarty)
{
    $url = function_exists('gm_get_env_info') ? gm_get_env_info('SCRIPT_NAME') : $_SERVER['SCRIPT_NAME'];
    $url = htmlspecialchars_wrapper($url);
    
    $basename = explode('?', basename($url));
    $basename = explode('.', $basename[0]);
    $basename = strtolower($basename[0]);
    $basename = 'page-' . str_replace('_', '-', $basename);
    
    if ($basename === 'page-index') {
        if (isset($_GET['cat'])) {
            $filenamePattern = '#\..*$#';
            
            $cat = preg_replace($filenamePattern, '', $_GET['cat']);
            
            $basename = 'page-index-type-' . htmlspecialchars($cat);
        }
        
        foreach ($_GET as $key => $value) {
            $basename .= ' page-index-type-' . htmlspecialchars_wrapper($key);
        }
        
        if ($_SESSION['coo_filter_manager']->v_filter_active) {
            $basename .= ' page-index-type-is-filtering';
        }
    } elseif (isset($_GET['checkout_started']) && $_GET['checkout_started'] === '1') {
        $basename .= ' page-checkout-started';
    } elseif (isset($_GET['do'])) {
        $basename .= '-' . htmlspecialchars(strtolower($_GET['do']));
    }
    
    return $basename;
}