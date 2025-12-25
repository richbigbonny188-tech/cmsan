<?php
/* --------------------------------------------------------------
   function.is_filemanager_available.php 2017-09-29 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
 */

/**
 * Returns whether the responsive file manager is available.
 *
 * @param                           $params
 * @param \Smarty_Internal_Template $template
 */
function smarty_function_is_filemanager_available($params, Smarty_Internal_Template $template)
{
    $isDirectoryExistent = is_dir(DIR_FS_CATALOG . 'ResponsiveFilemanager');
    $isActive            = gm_get_conf('MODULE_CENTER_RESPONSIVEFILEMANAGER_INSTALLED') === '1';
    
    $template->assign('filemanager_available', ($isDirectoryExistent && $isActive));
}