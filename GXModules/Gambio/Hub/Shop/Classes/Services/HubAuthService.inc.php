<?php

/* --------------------------------------------------------------
   HubAuthService.inc.php 2016-11-25
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
 * Class HubAuthService
 *
 * @category  System
 * @package   GambioHub
 */
class HubAuthService implements HubAuthServiceInterface
{
	/**
	 * @var HubAuthStorage
	 */
	protected $hubAuthStorage;
	
	/**
	 * @var HubClientKeyConfiguration
	 */
	protected $hubClientKeyConfiguration;
	
	
	/**
	 * HubAuthService constructor.
	 *
	 * @param \HubAuthStorageInterface   $hubAuthStorage            Used for the filesystem operations.
	 * @param \HubClientKeyConfiguration $hubClientKeyConfiguration Used for the database operations.
	 */
	public function __construct(HubAuthStorageInterface $hubAuthStorage,
	                            HubClientKeyConfiguration $hubClientKeyConfiguration)
	{
		$this->hubAuthStorage            = $hubAuthStorage;
		$this->hubClientKeyConfiguration = $hubClientKeyConfiguration;
	}
	
	
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
	public function authByAuthHash(AuthHash $authHash)
	{
		$verified = $this->hubAuthStorage->verifyAuthHashFile($authHash);
		
		if($verified)
		{
			$this->hubAuthStorage->removeAuthHashFile($authHash);
		}
		
		return $verified;
	}
	
	
	/**
	 * Authenticate by HubClientKey.
	 *
	 * This method will check if the provided HubClientKey is equal to the one that is stored in the database.
	 *
	 * @param \HubPublic\ValueObjects\HubClientKey $hubClientKey The key to be used for the authentication.
	 *
	 * @return bool Returns the authentication result.
	 */
	public function authByHubClientKey(HubClientKey $hubClientKey)
	{
		return $hubClientKey->asString() === $this->hubClientKeyConfiguration->get();
	}
}