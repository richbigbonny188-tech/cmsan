<?php
/* --------------------------------------------------------------
   xtc_create_random_value.inc.php 2017-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_create_random_value.inc.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_create_random_value.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

// include needed functions
if(!function_exists('xtc_rand'))
{
	require_once DIR_FS_INC . 'xtc_rand.inc.php';
}

function xtc_create_random_value($length, $type = 'mixed')
{
	if(!in_array($type, ['mixed', 'chars', 'digits'], true))
	{
		return false;
	}
	
	$randomValue = '';
	while(strlen($randomValue) < $length)
	{
		if($type === 'digits')
		{
			$char = xtc_rand(0, 9);
		}
		else
		{
			$char = chr(xtc_rand(0, 255));
		}
		
		if($type === 'mixed' && preg_match('/^[a-z0-9]$/i', $char))
		{
			$randomValue .= $char;
		}
		elseif($type === 'chars' && preg_match('/^[a-z]$/i', $char))
		{
			$randomValue .= $char;
		}
		elseif($type === 'digits' && preg_match('/^[\d]$/', $char))
		{
			$randomValue .= $char;
		}
	}
	
	return $randomValue;
}