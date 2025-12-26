<?php
/* --------------------------------------------------------------
   HubSessionKeyStorageInterface.inc.php 2016-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;
use \HubPublic\ValueObjects\HubSessionKey;

/**
 * Interface HubSessionKeyRepositoryInterface
 *
 * @category  System
 * @package   GambioHub
 * @subpackge Interfaces
 */
interface HubSessionKeyStorageInterface
{
	/**
	 * Stores the HubSessionKey and the filesystem.
	 *
	 * @param \HubPublic\ValueObjects\HubSessionKey $hubSessionKey The session key to be stored.
	 * @param \HubPublic\ValueObjects\AuthHash      $authHash      The authorization hash identifier.
	 *
	 * @return HubSessionKeyStorage Returns same class instance for chained method calls.
	 */
	public function store(HubSessionKey $hubSessionKey, AuthHash $authHash);
	
	
	/**
	 * Find the HubSessionKey in the filesystem and delete the file immediately.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The hub authorization hash identifier.
	 *
	 * @return \HubPublic\ValueObjects\HubSessionKey|null Returns a HubSessionKey instance or null if nothing
	 *                                                       was found.
	 */
	public function findAndDeleteByAuthHash(AuthHash $authHash);
}