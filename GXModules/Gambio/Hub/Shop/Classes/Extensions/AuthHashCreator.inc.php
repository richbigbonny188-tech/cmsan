<?php
/* --------------------------------------------------------------
   AuthHashCreator.inc.php 2017-02-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;

/**
 * Class AuthHashCreator
 */
class AuthHashCreator
{
	/**
	 * Creates and returns an Auth Hash
	 *
	 * @return \HubPublic\ValueObjects\AuthHash
	 */
	public static function create()
	{
		// Authentication hash.
		$authHash = new AuthHash(bin2hex(openssl_random_pseudo_bytes(16)));
		
		// Authentication hash file.
		@touch(DIR_FS_CATALOG . 'cache/hub_' . $authHash->asString());
		
		return $authHash;
	}
	
	
	/**
	 * Invalidates an Auth Hash by deleting its cache file
	 * 
	 * @param \HubPublic\ValueObjects\AuthHash $authHash
	 */
	public static function invalidate(AuthHash $authHash)
	{
		@unlink(DIR_FS_CATALOG . 'cache/hub_' . $authHash->asString());
	}
}
