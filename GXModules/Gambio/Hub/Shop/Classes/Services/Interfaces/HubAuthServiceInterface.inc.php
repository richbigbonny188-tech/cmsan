<?php

/* --------------------------------------------------------------
   HubAuthServiceInterface.inc.php 2016-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;
use \HubPublic\ValueObjects\HubClientKey;

/**
 * Interface HubSessionKeyServiceInterface
 *
 * @category   System
 * @package    GambioHub
 * @subpackage Interfaces
 */
interface HubAuthServiceInterface
{
	/**
	 * Authenticate by hash file in cache directory.
	 *
	 * This method will make sure that file with the provided hash as name, exists in the cache directory. It will
	 * remove the file afterwards for security reasons. Make sure that you create the file yourself.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The hash to be used for the authentication.
	 *
	 * @return bool Returns the authentication result.
	 */
	public function authByAuthHash(AuthHash $authHash);
	
	
	/**
	 * Authenticate by HubClientKey.
	 *
	 * This method will check if the provided HubClientKey is equal to the one that is stored in the database.
	 *
	 * @param \HubPublic\ValueObjects\HubClientKey $hubClientKey The key to be used for the authentication.
	 *
	 * @return bool Returns the authentication result.
	 */
	public function authByHubClientKey(HubClientKey $hubClientKey);
}