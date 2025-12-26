<?php
/* --------------------------------------------------------------
   function.menubox.php 2018-04-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @param $params
 * @param $smarty
 *
 * @return string
 *
 * @deprecated IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
 *             MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES.
 */
function smarty_function_menubox($params, &$smarty)
{
    $position = $GLOBALS['coo_template_control']->get_menubox_position($params['name']);
    
    // get box content
    $assignedVars = $smarty->getTemplateVars();
    foreach ($assignedVars as $title => $content) {
        if ($title === $position) {
            return $content;
        }
    }
    
    return '';
}
