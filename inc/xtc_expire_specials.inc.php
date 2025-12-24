<?php
/* --------------------------------------------------------------
   xtc_expire_specials.inc.php 2018-12-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

/* -----------------------------------------------------------------------------------------
   $Id: xtc_expire_specials.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(specials.php,v 1.5 2003/02/11); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_expire_specials.inc.php,v 1.5 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

require_once DIR_FS_INC . 'xtc_set_specials_status.inc.php';

/**
 * Auto activate/expire special offers
 */
function xtc_expire_specials()
{
	$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
	
	// disable expired special offers
	/** @var \CI_DB_result $specialsQuery */
	$specialsQuery = $db->select('specials_id')
	                    ->where('status = \'1\'')
	                    ->where('`expires_date` > \'1000-01-01 00:00:00\'')
	                    ->where('NOW() >= `expires_date`')
	                    ->get('specials');
	
	foreach($specialsQuery->result_array() as $row)
	{
		xtc_set_specials_status($row['specials_id'], false);
	}
	
	// enable special offers
	/** @var \CI_DB_result $specialsQuery */
	$specialsEnableQuery = $db->select('specials_id')
		->where('status = \'0\'')
		->where('started = \'0\'')
		->where('begins_date >= \'2018-01-01\'')
		->where('begins_date <= NOW()')
		->where('(expires_date > NOW() OR expires_date = \'1000-01-01\')')
		->get('specials');
	foreach($specialsEnableQuery->result_array() as $enableSpecial)
	{
		$db->where('specials_id = ' . $enableSpecial['specials_id'])
			->set('started', '1')
			->set('status', '1')
			->set('date_status_change', date('Y-m-d H:i:s'))
			->update('specials');
	}
	
}
