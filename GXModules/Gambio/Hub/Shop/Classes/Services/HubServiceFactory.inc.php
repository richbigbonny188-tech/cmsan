<?php

/* --------------------------------------------------------------
   HubServiceFactory.inc.php 2016-11-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface HubSessionKeyServiceInterface
 *
 * @category  System
 * @package   GambioHub
 */
class HubServiceFactory implements HubServiceFactoryInterface
{
	/**
	 * Create a HubSessionKeyService instance.
	 *
	 * @return HubSessionKeyServiceInterface
	 */
	public function createHubSessionKeyService()
	{
		$storageDirectoryPath = DIR_FS_CATALOG . 'cache';
		$hubSessionKeyStorage = MainFactory::create('HubSessionKeyStorage', $storageDirectoryPath);
		$hubSessionKeyService = MainFactory::create('HubSessionKeyService', $hubSessionKeyStorage);
		
		return $hubSessionKeyService;
	}
	
	
	/**
	 * Create a HubAuthService instance.
	 *
	 * @return HubAuthServiceInterface
	 */
	public function createHubAuthService()
	{
		$storageDirectoryPath      = DIR_FS_CATALOG . 'cache';
		$hubAuthStorage            = MainFactory::create('HubAuthStorage', $storageDirectoryPath);
		$hubClientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
		$hubAuthService            = MainFactory::create('HubAuthService', $hubAuthStorage, $hubClientKeyConfiguration);
		
		return $hubAuthService;
	}
}