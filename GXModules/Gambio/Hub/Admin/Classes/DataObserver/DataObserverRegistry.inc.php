<?php
/* --------------------------------------------------------------
   DataObserverRegistry.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\ValueObjects\HubSessionKey;

/**
 * Class DataObserverRegistry
 *
 * Gambio data observer facade, provides access to the core operations involved in informing Hub for data changes.
 *
 * Example:
 *
 * DataObserverRegistry::activate();
 * ...
 * DataObserverRegistry::inserted('order', $newOrderId);
 *
 * The registry will delegate all changes to Hub at the end of the script execution automatically.
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class DataObserverRegistry
{
	/**
	 * @var bool
	 */
	protected static $activated;
	
	/**
	 * @var array
	 */
	protected static $subjects;
	
	/**
	 * @var array
	 */
	protected static $observers;
	
	
	/**
	 * Activates the data observer registry.
	 *
	 * This method will enable the recording and delegation of data changes.
	 */
	public static function activate()
	{
		if(self::$activated || !DataObserverFeature::isActive())
		{
			return; // The registry is already activated.
		}
		
		self::$observers = [
			'order' => [
				MainFactory::create('OrderDataObserver')
			]
		];
		
		$factory = MainFactory::create('DataObserverFactory');
		
		self::$subjects = [
			'order' => MainFactory::create('OrderDataSubject', $factory, self::$observers['order'])
		];
		
		register_shutdown_function(['DataObserverRegistry', 'process']);
		
		self::$activated = true;
	}
	
	
	/**
	 * Record data insertion.
	 *
	 * Call this method with the appropriate subject and pass the inserted row ID.
	 *
	 * @param string $subject Subject codename.
	 * @param int    $rowId   Inserted row ID.
	 */
	public static function inserted($subject, $rowId)
	{
		self::change($subject, 'insert', $rowId);
	}
	
	
	/**
	 * Record data modification.
	 *
	 * Call this method with the appropriate subject and pass the inserted row ID.
	 *
	 * @param string $subject Subject codename.
	 * @param int    $rowId   Updated row ID.
	 */
	public static function updated($subject, $rowId)
	{
		self::change($subject, 'update', $rowId);
	}
	
	
	/**
	 * Record data removal.
	 *
	 * Call this method with the appropriate subject and pass the inserted row ID.
	 *
	 * @param string $subject Subject codename.
	 * @param int    $rowId   Removed row ID.
	 */
	public static function deleted($subject, $rowId)
	{
		self::change($subject, 'delete', $rowId);
	}
	
	
	/**
	 * @return array|bool|\DataChangeCollection
	 */
	protected static function resolve()
	{
		if(!self::$activated || !DataObserverFeature::isActive())
		{
			MainFactory::create('DataChangeCollection', []);
		}
		
		$dataChanges = [];
		
		foreach(self::$observers as $observerGroup)
		{
			foreach($observerGroup as $observer)
			{
				foreach($observer->getDataChanges() as $dataChange)
				{
					$dataChanges[] = $dataChange;
				}
			}
		}
		
		return MainFactory::create('DataChangeCollection', $dataChanges);
	}
	
	
	/**
	 * Process all recorded changes and delegate them to Hub.
	 *
	 * Once the "activate" method is called, this method is being set to run at the end of the script execution.
	 */
	public static function process()
	{
		try
		{
			if(!self::$activated || !DataObserverFeature::isActive())
			{
				return;
			}
			
			$dataChanges = DataObserverRegistry::resolve();
			
			if(count($dataChanges) === 0)
			{
				return;
			}
            
            $sessionKeyValue     = self::startSession();
            $sessionKey          = new HubSessionKey($sessionKeyValue);
			$dataObserverFactory = MainFactory::create('DataObserverFactory');
			$dataObserverService = $dataObserverFactory->createDataObserverService();
			
			try
			{
				$dataObserverService->processDataChanges($dataChanges, $sessionKey);
			}
			catch(HubSessionFailedException $exception)
			{
				// Hub session validation failed, retry with a new session.
				$sessionKeyValue = self::startSession();
				$sessionKey      = new HubSessionKey($sessionKeyValue);
				$dataObserverService->processDataChanges($dataChanges, $sessionKey);
			}
		}
		catch(Exception $exception)
		{
			$logControl = LogControl::get_instance();
			
			if($logControl !== null)
			{
				$logControl->notice('Could not process Hub data changes: ' . $exception->getMessage(), '', 'hub');
			}
		}
	}
	
	
	/**
	 * Delegate a data change in the correct subject.
	 *
	 * The correct subject observers will be eventually notified.
	 *
	 * @param string $subject Subject codename.
	 * @param string $action  Executed action, provide 'insert', 'update' or 'delete'.
	 * @param int    $rowId   Changed row ID.
	 */
	protected static function change($subject, $action, $rowId)
	{
		if(!self::$activated || !DataObserverFeature::isActive())
		{
			return;
		}
		
		if(empty(self::$subjects))
		{
			throw new \InvalidArgumentException('The data observer registry has no subjects.');
		}
		
		if(!isset(self::$subjects[$subject]))
		{
			throw new InvalidArgumentException('The provided subject is not registered in the data observer registry.');
		}
		
		self::$subjects[$subject]->change($action, $rowId);
	}
	
	
	/**
	 * Start a new Hub session.
	 *
	 * Use this method to create a new Hub session, in case there is non available to use.
	 *
	 * @return string
	 */
	protected static function startSession()
	{
		$apiClientFactory  = MainFactory::create('HubApiClientFactory');
		$sessionsApiClient = $apiClientFactory->createSessionsApiClient();
		$shopUrl           = HTTP_SERVER . DIR_WS_CATALOG;
		$languageCode      = new LanguageCode(new StringType(strtoupper(DEFAULT_LANGUAGE)));
		$authHash          = AuthHashCreator::create();
		
		return $sessionsApiClient->startSession($authHash, $shopUrl, $languageCode);
	}
}
