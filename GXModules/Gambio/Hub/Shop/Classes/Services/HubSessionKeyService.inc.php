<?php
/* --------------------------------------------------------------
   HubSessionKeyService.inc.php 2016-11-23
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
 * Class HubSessionKeyService
 *
 * This class provides methods for storing and finding customer hub session keys.
 *
 * @category System
 * @package  GambioHub
 */
class HubSessionKeyService implements HubSessionKeyServiceInterface
{
	/**
	 * @var HubSessionKeyStorageInterface
	 */
	protected $hubSessionKeyStorage;
	
	
	/**
	 * HubSessionKeyService constructor.
	 *
	 * @param \HubSessionKeyStorageInterface $hubSessionKeyStorage Used to handle the filesystem operations.
	 */
	public function __construct(HubSessionKeyStorageInterface $hubSessionKeyStorage)
	{
		$this->hubSessionKeyStorage = $hubSessionKeyStorage;
	}
	
	
	/**
	 * To store the HubSessionKey and the AuthHash, the method delegates to the HubSessionKeyRepository
	 *
	 * @param \HubPublic\ValueObjects\HubSessionKey $hubSessionKey The session key to be stored.
	 * @param \HubPublic\ValueObjects\AuthHash      $authHash      The authorization hash identifier.
	 *
	 * @return HubSessionKeyServiceInterface Returns same class instance for chained method calls.
	 */
	public function store(HubSessionKey $hubSessionKey, AuthHash $authHash)
	{
		$this->hubSessionKeyStorage->store($hubSessionKey, $authHash);
		
		return $this;
	}
	
	
	/**
	 * To find the HubSessionKey with the AuthHash, the method delegates to the HubSessionKeyRepository.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The hub authorization hash identifier.
	 *
	 * @return \HubPublic\ValueObjects\HubSessionKey|null Returns a HubSessionKey instance of null if nothing
	 *                                                       was found.
	 */
	public function findByAuthHash(AuthHash $authHash)
	{
		return $this->hubSessionKeyStorage->findAndDeleteByAuthHash($authHash);
	}
}