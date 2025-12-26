<?php
/* --------------------------------------------------------------
   HubCallbackHandlerInterface.inc.php 2017-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface HubCallbackHandlerInterface
 */
interface HubCallbackHandlerInterface
{
	/**
	 * Stores the hub client key and the shop key, sends a http response code header and returns a json response array.
	 *
	 * @param string $authHash
	 * @param string $clientKey
	 * @param string $shopKey
	 *
	 * @return array Json Response Array
	 */
	public function proceedClientKeyCallback($authHash, $clientKey, $shopKey);
	
	
	/**
	 * Stores the hub session key, sends a http response code header and returns a json response array.
	 *
	 * @param string $authHash
	 * @param string $sessionKey
	 *
	 * @return array Json Response Array
	 */
	public function proceedSessionKeyCallback($authHash, $sessionKey);
	
	
	/**
	 * Updates the status of an order, sends a http response code header and returns a json response array.
	 *
	 * @param string $clientKey     Client key.
	 * @param int    $orderId       Order Id.
	 * @param int    $orderStatusId Order status Id.
	 *
	 * @return array Json Response Array
	 */
	public function proceedUpdateOrderStatusCallback($clientKey, $orderId, $orderStatusId);
	
	
	/**
	 * Inserts a new order status name, sends a http response code header and returns a json response array.
	 *
	 * @param string $clientKey        Client key.
	 * @param array  $orderStatusArray Order status array.
	 *
	 * @return array Json Response Array
	 */
	public function proceedCreateOrderStatusCallback($clientKey, $orderStatusArray);
	
	
	/**
	 * Inserts into gm_configuration or updates gm_configuration with given key and value, sends a http response code
	 * header and returns a json response array.
	 *
	 * @param string $clientKey          HubClientKey
	 * @param string $configurationKey   Determines which gm_configuration key should be updated
	 * @param string $configurationValue Determines to which value the key should be set
	 *
	 * @return array Json Response Array
	 */
	public function proceedUpdateConfiguration($configurationKey, $configurationValue, $clientKey);
	
	
	/**
	 * Reads from gm_configuration with given key, sends a http response code header and returns a json response array.
	 *
	 * @param string $clientKey        HubClientKey
	 * @param string $configurationKey Determines which gm_configuration key should be retrieved
	 *
	 * @return array Json Response Array
	 */
	public function proceedGetConfiguration($clientKey, $configurationKey);
}