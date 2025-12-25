<?php
/* --------------------------------------------------------------
  xtc_get_path.inc.php 2019-06-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
  (c) 2003	 nextcommerce (xtc_get_path.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_path.inc.php 1009 2005-07-11 16:19:29Z mz $)

  Released under the GNU General Public License
  -------------------------------------------------------------- */

function xtc_get_path($currentCategoryId = '')
{
    if (empty($currentCategoryId)) {
        $cPathNew = implode('_', $GLOBALS['cPath_array']);
    } elseif (!isset($GLOBALS['cPath_array']) || !is_array($GLOBALS['cPath_array']) || count($GLOBALS['cPath_array']) === 0) {
        $cPathNew = $currentCategoryId;
    } else {
        $categoryId  = (int)$currentCategoryId;
        $killSwitch  = 100; // needed for anomalies in DB (endless parent/child connections)
        $categoryIds = [$categoryId];
        
        while ($categoryId !== 0 && $killSwitch) {
            $query  = 'SELECT `parent_id` FROM `categories` WHERE `categories_id` = ' . $categoryId;
            $result = xtc_db_query($query);
            if (xtc_db_num_rows($result)) {
                $row        = xtc_db_fetch_array($result);
                $categoryId = (int)$row['parent_id'];
                
                if ($categoryId) {
                    array_unshift($categoryIds, $categoryId);
                }
            } else {
                $categoryId = 0;
            }
            
            $killSwitch--;
        }
        
        $cPathNew = implode('_', $categoryIds);
    }
    
    return 'cPath=' . $cPathNew;
}