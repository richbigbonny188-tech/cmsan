<?php
/* --------------------------------------------------------------
   KlarnaHubFactory.inc.php 2017-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class KlarnaHubFactory
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class KlarnaHubFactory
{
	/**
	 * @var KlarnaHubCallbackClient
	 */
	protected $callbackClient;
	
	/**
	 * @var KlarnaHubConfiguration
	 */
	protected $configuration;
	
	
	/**
	 * Creates Klarna Hub callback client instance.
	 *
	 * @param NonEmptyStringType $moduleCode  Module code.
	 * @param NonEmptyStringType $orderNumber Order number.
	 *
	 * @return KlarnaHubCallbackClient
	 */
	public function createCallbackClient(NonEmptyStringType $moduleCode, NonEmptyStringType $orderNumber)
	{
		if($this->callbackClient === null)
		{
			$url = new NonEmptyStringType(MODULE_PAYMENT_GAMBIO_HUB_URL . '/payment_modules/' . $moduleCode->asString()
			                              . '/callback');
			
			$clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
			
			$clientKey = $clientKeyConfiguration->getClientKey();
			
			$this->callbackClient = MainFactory::create('KlarnaHubCallbackClient', $url, $clientKey, $orderNumber);
		}
		
		return $this->callbackClient;
	}
	
	
	/**
	 * Creates a Klarna Hub Configuration instance.
	 *
	 * @return KlarnaHubConfiguration
	 */
	public function createConfiguration()
	{
		if($this->configuration === null)
		{
			$languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_klarna_hub',
			                                           $_SESSION['languages_id']);
			$countries           = MainFactory::create('Countries', $_SESSION['languages_id']);
			$orderReadService    = StaticGXCoreLoader::getService('OrderRead');
			$orderJsonSerializer = MainFactory::create('OrderJsonSerializer');
			
			$this->configuration = MainFactory::create('KlarnaHubConfiguration', $languageTextManager, $countries,
			                                           $orderReadService, $orderJsonSerializer, $this);
		}
		
		return $this->configuration;
	}
}