<?php
/* --------------------------------------------------------------
   DataObserverApiClient.inc.php 2018-10-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;
use \HubPublic\ValueObjects\HubSessionKey;

/**
 * Class DataObserverApiClient
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class DataObserverApiClient
{
	/**
	 * @var \HubPublic\Http\CurlRequest
	 */
	private $curlRequest;
	
	/**
	 * @var \HubSettings
	 */
	protected $hubSettings;
	
	
	/**
	 * DataObserverApiClient constructor.
	 *
	 * @param \HubPublic\Http\CurlRequest $curlRequest CurlRequest instance.
	 * @param \HubSettings                $hubSettings HubSettings instance.
	 */
	public function __construct(CurlRequest $curlRequest, HubSettings $hubSettings)
	{
		$this->curlRequest = $curlRequest;
		$this->hubSettings = $hubSettings;
	}
	
	
	/**
	 * Delegates data changes to Hub.
	 *
	 * @param \DataChangeCollection                 $dataChanges Contains data changes.
	 * @param \HubPublic\ValueObjects\HubSessionKey $sessionKey  Active Hub session key.
	 *
	 * @return \DataObserverApiClient Returns same instance for chained method calls.
	 *
	 * @throws \HubSessionFailedException If Hub session validation fails.
	 * @throws \UnexpectedValueException If the response status code is not 202.
	 */
	public function delegateDataChanges(DataChangeCollection $dataChanges, HubSessionKey $sessionKey)
	{
		$url = $this->hubSettings->getHubUrl() . '/sessions/' . $sessionKey->asString() . '/data_changes';
		
		$body = json_encode($dataChanges);
		
		$response = $this->curlRequest->setUrl($url)
		                              ->setOption(CURLOPT_POST, true)
		                              ->setOption(CURLOPT_POSTFIELDS, $body)
		                              ->setOption(CURLOPT_HTTPHEADER, [
			                              'Expect:',
			                              'Content-Type: application/json',
			                              'X-Shop-Key: ' . $this->hubSettings->getShopKey(),
			                              'X-Client-Key: ' . $this->hubSettings->getClientKey(),
			                              'X-Origin-Client-Url: ' . $this->hubSettings->getShopUrl()
		                              ])
		                              ->execute();
		
		if($response->getStatusCode() === 401)
		{
			throw new \HubSessionFailedException('Failed to validate the used session, retry with a new one. '
			                                     . $response->getBody() . ' (' . $response->getStatusCode() . ')');
		}
		
		if($response->getStatusCode() !== 202)
		{
			throw new \UnexpectedValueException('Failed to delegate data changes. ' . $response->getBody() . ' ('
			                                    . $response->getStatusCode() . ')');
		}
		
		return $this;
	}
}
