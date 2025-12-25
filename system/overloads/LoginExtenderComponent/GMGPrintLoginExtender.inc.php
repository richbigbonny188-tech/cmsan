<?php
/* --------------------------------------------------------------
   GMGPrintLoginExtender.inc.php 2022-04-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMGPrintLoginExtender extends GMGPrintLoginExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		if($this->hasSessionKey('coo_gprint_cart') && is_object($_SESSION['coo_gprint_cart']))
		{
			$_SESSION['coo_gprint_cart']->restore();
		}
		if($this->hasSessionKey('coo_gprint_wishlist') && is_object($_SESSION['coo_gprint_wishlist']))
		{
			$_SESSION['coo_gprint_wishlist']->restore();
		}		
	}
    
    
    private function hasSessionKey(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }
}
?>