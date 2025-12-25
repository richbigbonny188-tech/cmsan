<?php
/* --------------------------------------------------------------
   DataObserverFactory.inc.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

/**
 * Class DataObserverFactory
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class DataObserverFactory
{
	/**
	 * @var \CI_DB_query_builder
	 */
	protected $queryBuilder;
	
	/**
	 * @var \HubPublic\Http\CurlRequest
	 */
	protected $curlRequest;
	
	/**
	 * @var \DataObserverApiClient
	 */
	protected $dataObserverApiClient;
	
	/**
	 * @var \HubSettings
	 */
	protected $hubSettings;
	
	
	/**
	 * Creates a data observer service object.
	 *
	 * @return \DataObserverService
	 */
	public function createDataObserverService()
	{
		return MainFactory::create('DataObserverService', $this->_createDataObserverApiClient());
	}
	
	
	/**
	 * Creates a data change instance.
	 *
	 * @param string $action   Executed change action, provide 'insert', 'update' or 'delete'.
	 * @param string $table    Database table of changed record.
	 * @param string $idColumn Database ID column name of the table.
	 * @param int    $rowId    Changed row ID.
	 *
	 * @return \DataChange
	 */
	public function createDataChange($action, $table, $idColumn, $rowId)
	{
		return MainFactory::create('DataChange', $this->_createQueryBuilder(), $action, $table, $idColumn, $rowId);
	}
	
	
	/**
	 * Creates a data observer api client object.
	 *
	 * @return \DataObserverApiClient
	 */
	protected function _createDataObserverApiClient()
	{
		if($this->dataObserverApiClient === null)
		{
			$this->dataObserverApiClient = MainFactory::create('DataObserverApiClient', $this->_createCurlRequest(),
			                                                   $this->_createHubSettings());
		}
		
		return $this->dataObserverApiClient;
	}
	
	
	/**
	 * Creates a database query builder object.
	 *
	 * @return \CI_DB_query_builder
	 */
	protected function _createQueryBuilder()
	{
		if($this->queryBuilder === null)
		{
			$this->queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
		}
		
		return $this->queryBuilder;
	}
	
	
	/**
	 * Creates a curl request object.
	 *
	 * @return \HubPublic\Http\CurlRequest
	 */
	protected function _createCurlRequest()
	{
		if($this->curlRequest === null)
		{
			$this->curlRequest = new CurlRequest();
		}
		
		return $this->curlRequest;
	}
	
	
	/**
	 * Creates a hub settings object.
	 *
	 * @return \HubSettings
	 */
	protected function _createHubSettings()
	{
		if($this->hubSettings === null)
		{
			$this->hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
		}
		
		return $this->hubSettings;
	}
}
