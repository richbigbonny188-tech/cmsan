<?php

/* --------------------------------------------------------------
   HubAuthStorageInterface.inc.php 2016-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;

/**
 * Interface HubSessionKeyRepositoryInterface
 *
 * @category   System
 * @package    GambioHub
 * @subpackage Interfaces
 */
interface HubAuthStorageInterface
{
	/**
	 * Verify that an authentication hash file exists in the cache directory.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The authentication hash to be verified.
	 *
	 * @return bool Returns the verification result.
	 */
	public function verifyAuthHashFile(AuthHash $authHash);
	
	
	/**
	 * Remove authentication hash file from filesystem.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The authentication hash to be removed.
	 *
	 * @return HubAuthStorageInterface Returns same instance for chained method calls.
	 *
	 * @throws InvalidArgumentException If the authentication file does not exist.
	 */
	public function removeAuthHashFile(AuthHash $authHash);
}