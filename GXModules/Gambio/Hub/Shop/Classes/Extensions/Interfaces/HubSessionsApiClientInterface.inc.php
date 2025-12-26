<?php
/* --------------------------------------------------------------
   HubSessionsApiClientInterface.inc.php 2017-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\AuthHash;

/**
 * Interface HubSessionApiClientInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
interface HubSessionsApiClientInterface
{
	/**
	 * Starts a session in the Gambio Hub.
	 *
	 * Provide an authorization hash that can be later used in the hub callbacks to determine where each
	 * session key belongs to. This method will additionally save the AuthHash value to the PHP session with
	 * the key 'gambio_hub_auth_hash'.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash The authorization hash to be used for the session start.
	 * @param string                           $shopUrl  Shop url with trailing slash.
	 * @param \LanguageCode                    $languageCode
	 *
	 * @return string Returns the new session key.
	 */
	public function startSession(AuthHash $authHash, $shopUrl, LanguageCode $languageCode);
}