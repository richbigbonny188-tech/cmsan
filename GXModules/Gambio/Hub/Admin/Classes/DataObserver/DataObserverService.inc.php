<?php
/* --------------------------------------------------------------
   DataObserverService.inc.php 2019-01-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\HubSessionKey;

/**
 * Class DataObserverService
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class DataObserverService
{
	/**
	 * @var \DataObserverApiClient
	 */
	private $apiClient;
	
	
	/**
	 * DataObserverService constructor.
	 *
	 * @param \DataObserverApiClient $apiClient
	 */
	public function __construct(DataObserverApiClient $apiClient)
	{
		$this->apiClient = $apiClient;
	}
	
	
	/**
	 * Runs through all registered data changes and delegates them to Hub.
	 *
	 * @param \DataChangeCollection                 $dataChanges Data changes to be processed.
	 * @param \HubPublic\ValueObjects\HubSessionKey $sessionKey  Active Hub session key.
	 *
	 * @return \DataObserverService Returns same instance for chained method calls.
	 *
	 * @throws \HubSessionFailedException If Hub session validation fails.
	 */
	public function processDataChanges(DataChangeCollection $dataChanges, HubSessionKey $sessionKey)
	{
		if(count($dataChanges) === 0)
		{
			return $this; // Do not process an empty data changes collection.
		}
		
		$this->apiClient->delegateDataChanges($dataChanges, $sessionKey);
		
		return $this;
	}
}
