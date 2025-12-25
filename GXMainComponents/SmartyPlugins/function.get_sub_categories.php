<?php
/* --------------------------------------------------------------
   function.get_sub_categories.php 2018-02-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_get_sub_categories($params, &$smarty)
{
    $col = 0;
    $col++;
    
    $mod = $col % MAX_DISPLAY_CATEGORIES_PER_ROW;
    if ($mod === 0 && defined('GM_CAT_COUNT') && $col != GM_CAT_COUNT) {
        echo '</ul><ul class="sub_categories_listing_body">';
    }
}