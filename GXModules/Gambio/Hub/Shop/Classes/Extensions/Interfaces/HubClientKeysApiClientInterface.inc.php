<?php

/* --------------------------------------------------------------
   HubClientKeysApiClientInterface.inc.php 2016-12-23
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;

/**
 * Interface HubClientKeysApiClientInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
interface HubClientKeysApiClientInterface
{
	/**
	 * Creates a client key in the Gambio Hub.
	 *
	 * Provide an authorization hash that can be later used in the hub callbacks to determine where each
	 * session key belongs to. This method will additionally save the AuthHash value to the PHP session with
	 * the key 'gambio_hub_auth_hash' for later reference.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash    The authorization hash to be used.
	 * @param string                           $shopUrl     Shop URL (with trailing slash).
	 * @param string                           $shopVersion Current shop version (without leading "v").
	 *
	 * @return \HubPublic\ValueObjects\HubClientKey Returns the new client key.
	 *
	 * @throws UnexpectedValueException If the server responses with status code different to 201.
	 */
	public function createClientKey(AuthHash $authHash, $shopUrl, $shopVersion);
}