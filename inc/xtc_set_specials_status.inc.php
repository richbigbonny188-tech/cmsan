<?php
/* --------------------------------------------------------------
   xtc_set_specials_status.inc.php 2018-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/* -----------------------------------------------------------------------------------------
   $Id: xtc_set_specials_status.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(specials.php,v 1.5 2003/02/11); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_set_specials_status.inc.php,v 1.3 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

/**
 * Sets the status of a special offer price
 *
 * @param int $specials_id
 * @param mixed $status evaluated as boolean indicating new status
 */
function xtc_set_specials_status($specials_id, $status)
{
	$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
	$db->set([
		         'status'             => (bool)$status ? '1' : '0',
		         'date_status_change' => date('Y-m-d H:i:s')
	         ])
	   ->where(['specials_id' => (int)$specials_id])
	   ->update('specials');
}
