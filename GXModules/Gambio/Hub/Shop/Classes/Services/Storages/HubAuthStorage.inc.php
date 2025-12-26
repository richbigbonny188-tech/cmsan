<?php

/* --------------------------------------------------------------
   HubAuthStorage.inc.php 2016-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;

/**
 * Class HubAuthStorage
 *
 * Performs the filesystem operations for the HubAuthService.
 *
 * @category   System
 * @package    GambioHub
 * @subpackage Storages
 */
class HubAuthStorage implements HubAuthStorageInterface
{
	/**
	 * @var string
	 */
	protected $storageDirectoryPath;
	
	
	/**
	 * HubAuthStorage constructor.
	 *
	 * @param string $storageDirectoryPath The storage directory path where the authentication files are stored.
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
	 * Verify that an authentication hash file exists in the cache directory.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The authentication hash to be verified.
	 *
	 * @return bool Returns the verification result.
	 */
	public function verifyAuthHashFile(AuthHash $authHash)
	{
		return file_exists($this->storageDirectoryPath . '/hub_' . $authHash->asString());
	}
	
	
	/**
	 * Remove authentication hash file from filesystem.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The authentication hash to be removed.
	 *
	 * @return HubAuthStorageInterface Returns same instance for chained method calls.
	 *
	 * @throws InvalidArgumentException If the authentication file does not exist.
	 */
	public function removeAuthHashFile(AuthHash $authHash)
	{
		if(!$this->verifyAuthHashFile($authHash))
		{
			throw new InvalidArgumentException('Authentication file does not exist: ' . $authHash->asString());
		}
		
		@unlink($this->storageDirectoryPath . '/hub_' . $authHash->asString());
		
		return $this;
	}
}