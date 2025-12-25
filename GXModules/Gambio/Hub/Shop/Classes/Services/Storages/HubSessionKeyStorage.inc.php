<?php
/* --------------------------------------------------------------
   HubSessionKeyStorage.inc.php 2017-11-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;
use \HubPublic\ValueObjects\HubSessionKey;

/**
 * Class HubSessionKeyStorage
 *
 * @category   System
 * @package    GambioHub
 * @subpackage Storages
 */
class HubSessionKeyStorage implements HubSessionKeyStorageInterface
{
	/**
	 * @var string
	 */
	protected $storageDirectoryPath;
	
	
	/**
	 * HubSessionKeyStorage constructor.
	 *
	 * @param string $storageDirectoryPath The storage directory path where the session keys will be stored.
	 *
	 * @throws InvalidArgumentException If the storage directory path does not exist or is not writable.
	 */
	public function __construct($storageDirectoryPath)
	{
		if(!file_exists($storageDirectoryPath) || !is_writable($storageDirectoryPath))
		{
			throw new InvalidArgumentException('The storage directory does not exist or is not writable: '
			                                   . $storageDirectoryPath);
		}
		
		$this->storageDirectoryPath = rtrim($storageDirectoryPath, '\\/');
	}
	
	
	/**
	 * Stores the HubSessionKey in the filesystem.
	 *
	 * @param \HubPublic\ValueObjects\HubSessionKey $hubSessionKey The session key to be stored.
	 * @param \HubPublic\ValueObjects\AuthHash      $authHash      The authorization hash identifier.
	 *
	 * @return HubSessionKeyStorage Returns same class instance for chained method calls.
	 */
	public function store(HubSessionKey $hubSessionKey, AuthHash $authHash)
	{
		$filename = $this->_getFilename($authHash);
		
		file_put_contents($filename, $hubSessionKey->asString());
		
		return $this;
	}
	
	
	/**
	 * Find the HubSessionKey in the filesystem and delete the file immediately.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The hub authorization hash identifier.
	 *
	 * @return \HubPublic\ValueObjects\HubSessionKey|null Returns a HubSessionKey instance or null if nothing
	 *                                                       was found.
	 */
	public function findAndDeleteByAuthHash(AuthHash $authHash)
	{
		$filename = $this->storageDirectoryPath . '/hub_' . $authHash->asString();
		
		if(file_exists($filename) && is_readable($filename))
		{
			$fileContent = file_get_contents($filename);
			
			if($fileContent === '')
			{
				return null;
			}
			
			unlink($filename);
			
			return new HubSessionKey($fileContent);
		}
		
		return null;
	}
	
	
	/**
	 * Get storage filename for given authorization hash identifier.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The hub session authorization identifier.
	 *
	 * @return string
	 */
	public function _getFilename(AuthHash $authHash)
	{
		return $this->storageDirectoryPath . '/hub_' . $authHash->asString();
	}
}