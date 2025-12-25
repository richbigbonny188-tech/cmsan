<?php
/* --------------------------------------------------------------
   HubApiClientFactory.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Http\CurlRequest;

/**
 * Class HubApiClientFactory
 *
 * Creates API client instances.
 */
class HubApiClientFactory
{
	/**
	 * Creates a HubSessionsApiClient instance.
	 *
	 * @return \HubSessionsApiClient
	 */
	public function createSessionsApiClient()
	{
		$serviceFactory         = MainFactory::create('HubServiceFactory');
		$sessionKeyService      = $serviceFactory->createHubSessionKeyService();
		$clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
		$curlRequest            = new CurlRequest();
		$logControl             = LogControl::get_instance();
		$hubSettings            = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
		$sessionsApiClient      = MainFactory::create('HubSessionsApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
		                                              $sessionKeyService, $clientKeyConfiguration, $curlRequest,
		                                              $logControl, $hubSettings);
		
		/** @var \HubSessionsApiClient $sessionsApiClient */
		return $sessionsApiClient;
	}
}
