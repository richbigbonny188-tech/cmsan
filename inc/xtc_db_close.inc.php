<?php
/* --------------------------------------------------------------
  xtc_db_close.inc.php 2021-07-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com
  (c) 2003	 nextcommerce (xtc_db_close.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_close.inc.php 899 2005-04-29 02:40:57Z hhgag $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

function xtc_db_close($p_link = 'db_link')
{
    $mysqli = $GLOBALS[$p_link];
    $result = false;
    
    if ($mysqli instanceof mysqli) {
        if (class_exists('StaticGXCoreLoader', false)) {
            $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $queryBuilder->close();
            $result = true;
        } else {
            $result = ((is_null($___mysqli_res = mysqli_close($mysqli))) ? false : $___mysqli_res);
        }
    }
    
    return $result;
}