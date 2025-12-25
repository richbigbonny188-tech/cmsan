<?php

/* --------------------------------------------------------------
   HubServiceFactoryInterface.inc.php 2016-11-21
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
 * @category   System
 * @package    GambioHub
 * @subpackage Interfaces
 */
interface HubServiceFactoryInterface
{
	/**
	 * Create a HubSessionKeyService instance.
	 *
	 * @return HubSessionKeyServiceInterface
	 */
	public function createHubSessionKeyService();
	
	
	/**
	 * Create a HubAuthService instance.
	 *
	 * @return HubAuthServiceInterface
	 */
	public function createHubAuthService();
}