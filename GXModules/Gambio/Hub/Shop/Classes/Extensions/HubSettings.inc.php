<?php
/* --------------------------------------------------------------
   HubSettings.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HubSettings
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
class HubSettings
{
	/**
	 * @var int
	 */
	protected $curlTimeout;
	
	
	/**
	 * HubSettings constructor.
	 *
	 * @param int $curlTimeout curl timeout in seconds
	 */
	public function __construct($curlTimeout)
	{
		$this->curlTimeout = (int)$curlTimeout;
	}
	
	
	/**
	 * Returns curl timeout in seconds.
	 *
	 * @return int
	 */
	public function getCurlTimeout()
	{
		return $this->curlTimeout;
	}
	
	
	/**
	 * Returns the Hub URL, used for API calls.
	 *
	 * @return string
	 */
	public function getHubUrl()
	{
		return defined('MODULE_PAYMENT_GAMBIO_HUB_URL') ? MODULE_PAYMENT_GAMBIO_HUB_URL : '';
	}
	
	
	/**
	 * Returns the current shop key.
	 *
	 * @return string
	 */
	public function getShopKey()
	{
		return defined('GAMBIO_SHOP_KEY') ? GAMBIO_SHOP_KEY : '';
	}
	
	
	/**
	 * Returns the shop URL.
	 *
	 * @return string
	 */
	public function getShopUrl()
	{
		return HTTP_SERVER . DIR_WS_CATALOG;
	}
	
	
	/**
	 * Returns the client key.
	 *
	 * @var string
	 *
	 * @return string
	 */
	public function getClientKey()
	{
		return (string)gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
	}
}