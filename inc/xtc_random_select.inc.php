<?php
/* --------------------------------------------------------------
   xtc_random_select.inc.php 2022-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_random_select.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_random_select.inc.php 1108 2022-11-25 20:24:08Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once DIR_FS_INC . 'xtc_db_data_seek.inc.php';
require_once DIR_FS_INC . 'xtc_rand.inc.php';

function xtc_random_select($query)
{
    $random_product = '';
    $random_query   = xtc_db_query($query);
    $num_rows       = xtc_db_num_rows($random_query);
    if ($num_rows > 0) {
        $random_row = xtc_rand(0, ($num_rows - 1));
        xtc_db_data_seek($random_query, $random_row);
        $random_product = xtc_db_fetch_array($random_query);
    }
    
    return $random_product;
}