<?php
/* --------------------------------------------------------------
   function.check_mailbeez_menue.php
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_check_mailbeez_menu($params, Smarty_Internal_Template $template)
{
    if (function_exists('mailBeezMenu')) {
        $template->assign("mailbeez_menu", mailBeezMenu());
    }
}