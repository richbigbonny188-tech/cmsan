<?php
/* --------------------------------------------------------------
   xtc_validate_password.inc.php 2016-09-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(password_funcs.php,v 1.10 2003/02/11); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_validate_password.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_validate_password.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// This funstion validates a plain text password with an encrpyted password
function xtc_validate_password($plain, $encrypted)
{
	/** @var AuthService $authService */
	$authService = StaticGXCoreLoader::getService('Auth');
	
	return $authService->verify(new StringType($plain), new NonEmptyStringType($encrypted));
}