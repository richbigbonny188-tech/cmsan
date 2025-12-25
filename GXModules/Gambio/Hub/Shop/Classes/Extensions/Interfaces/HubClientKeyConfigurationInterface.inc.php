<?php

/* --------------------------------------------------------------
   HubClientKeyConfigurationInterface.inc.php 2017-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface HubClientKeyConfigurationInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
interface HubClientKeyConfigurationInterface
{
	/**
	 * Set the hub client key in the database configuration table.
	 *
	 * @param \HubPublic\ValueObjects\HubClientKey $clientKey
	 *
	 * @return HubClientKeyConfiguration Returns same instance for chained method calls.
	 */
	public function set(\HubPublic\ValueObjects\HubClientKey $clientKey);
	
	
	/**
	 * Get the hub client key from the database.
	 *
	 * @return string|null Returns the hub client key or null if none set.
	 */
	public function get();
	
	
	/**
	 * Returns the hub client key as a HubClientKey instance.
	 *
	 * @return \HubPublic\ValueObjects\HubClientKey
	 */
	public function getClientKey();
}