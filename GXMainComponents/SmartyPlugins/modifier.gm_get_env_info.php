<?php
/* --------------------------------------------------------------
   modifier.gm_get_conf.php 2016-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_modifier_gm_get_env_info($string)
{
    require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_env_info.inc.php');
    
    $output = gm_get_env_info($string);
    
    return $output;
}