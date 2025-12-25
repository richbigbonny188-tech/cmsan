<?php
/* --------------------------------------------------------------
   PaymentMatchingHubController.inc.php 2022-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;

/**
 * Class PaymentMatchingHubController
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class PaymentMatchingHubController extends AdminHttpViewController
{
	/**
	 * @var string
	 */
	const GET_USER_INTERFACE_URL = 'get-user-interface-url';
	
	/**
	 * @var string
	 */
	protected $configurationKey = 'GAMBIO_HUB_MECO_MEDIA_SHOP_ID';
	
	/**
	 * @var string
	 */
	protected $moneyOrderConfigurationKey = 'GAMBIO_HUB_MECO_MEDIA_MONEY_ORDER';
	
	/**
	 * @var string
	 */
	protected $invoiceConfigurationKey = 'GAMBIO_HUB_MECO_MEDIA_INVOICE';
	
	/**
	 * @var string
	 */
	protected $scriptsBaseUrl;
	
	/**
	 * @var string
	 */
	protected $stylesBaseUrl;
	
	/**
	 * @var string
	 */
	protected $minPostfix;
	
	/**
	 * @var LanguageTextManager
	 */
	protected $languageTextManager;
	
	/**
	 * @var HubAssetHelper
	 */
	protected $hubAssetHelper;
	
	/**
	 * @var \HubClientKeyConfiguration
	 */
	protected $clientKeyConfiguration;
	
	
	/**
	 * Initialize the controller.
	 */
	public function init()
	{
		if(isset($_SESSION['coo_page_token']) == false)
		{
			throw new RuntimeException('Page Token Generator not found.'); // CSRF Protection
		}
		
		$installedVersion             = gm_get_conf('INSTALLED_VERSION');
		$this->hubAssetHelper         = MainFactory::create('HubAssetHelper', $installedVersion);
		$this->clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
		
		$this->scriptsBaseUrl      = DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl();
		$this->stylesBaseUrl       = DIR_WS_CATALOG . $this->hubAssetHelper->getStylesBaseUrl();
		$this->minPostfix          = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_payment_matching_hub',
		                                                 $_SESSION['languages_id']);
	}
	
	
	/**
	 * Renders the money order interface page.
	 *
	 * @return HttpControllerResponseInterface
	 */
	public function actionDefault()
	{
		$title = new NonEmptyStringType($this->languageTextManager->get_text('PAGE_TITLE',
		                                                                     'gambio_hub_payment_matching_hub'));
		
		$template = new ExistingFile(new NonEmptyStringType(DIR_FS_CATALOG
		                                                    . $this->hubAssetHelper->getTemplatesBasePath()
		                                                    . '/payment_matching.html'));
		
		$shopId = gm_get_conf($this->configurationKey);
		
		if(empty($shopId))
		{
			$this->displayInfoboxMessage($this->languageTextManager->get_text('PAGE_TITLE',
			                                                                  'gambio_hub_payment_matching_hub'),
			                             $this->languageTextManager->get_text('PAYMENT_MATCHING_INTEGRATION_IS_DISABLED_SHOP_NOT_REGISTERED',
			                                                                  'gambio_hub_payment_matching_hub'));
			
			return MainFactory::create(RedirectHttpControllerResponse::class,
			                           DIR_WS_ADMIN . 'admin.php?do=HubConfiguration/PaymentMethods');
		}
		
		$isMoneyOrderActive = gm_get_conf($this->moneyOrderConfigurationKey);
		$isInvoiceActive    = gm_get_conf($this->invoiceConfigurationKey);
		
		if($isMoneyOrderActive !== 'true' && $isInvoiceActive !== 'true') // Those config entries are stored in lowercase (true/false).
		{
			$this->displayInfoboxMessage($this->languageTextManager->get_text('PAGE_TITLE',
			                                                                  'gambio_hub_payment_matching_hub'),
			                             $this->languageTextManager->get_text('PAYMENT_MATCHING_INTEGRATION_IS_DISABLED_MODULE_NOT_ACTIVE',
			                                                                  'gambio_hub_payment_matching_hub'));
			
			return MainFactory::create(RedirectHttpControllerResponse::class,
			                           DIR_WS_ADMIN . 'admin.php?do=HubConfiguration/PaymentMethods');
		}
		
		$userInterfaceUrl = $this->getUserInterfaceUrl();
		
		if(empty($userInterfaceUrl))
		{
			$this->displayInfoboxMessage($this->languageTextManager->get_text('PAGE_TITLE',
			                                                                  'gambio_hub_payment_matching_hub'),
			                             $this->languageTextManager->get_text('USER_INTERFACE_URL_COULD_NOT_BE_FETCHED',
			                                                                  'gambio_hub_payment_matching_hub'));
			
			return MainFactory::create(RedirectHttpControllerResponse::class,
			                           DIR_WS_ADMIN . 'admin.php?do=HubConfiguration/PaymentMethods');
		}
		
		$data = MainFactory::create('KeyValueCollection', [
			'iframe_source_url' => $userInterfaceUrl . '&language=' . $_SESSION['language_code']
		]);
		
		$assetsArray = [
			MainFactory::create('Asset', $this->scriptsBaseUrl . '/vendor/promise' . $this->minPostfix . '.js'),
			MainFactory::create('Asset', $this->scriptsBaseUrl . '/vendor/fetch' . $this->minPostfix . '.js'),
			MainFactory::create('Asset', $this->scriptsBaseUrl . '/vendor/iframe_resizer' . $this->minPostfix . '.js'),
			MainFactory::create('Asset', $this->stylesBaseUrl . '/gambio_hub' . $this->minPostfix . '.css')
		];
		
		$assets = MainFactory::create('AssetCollection', $assetsArray);
		
		return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
	}
	
	
	/**
	 * Generates access token for PaymentMatchingHub.
	 *
	 * @return string
	 */
	private function getUserInterfaceUrl()
	{
		$params = [
			'action'     => self::GET_USER_INTERFACE_URL,
			'source'     => 'shop',
			'client_key' => $this->clientKeyConfiguration->get(),
			'shop_id'    => gm_get_conf($this->configurationKey)
		];
		
		$url = MODULE_PAYMENT_GAMBIO_HUB_URL . '/payment_modules/MoneyOrderHub/callback?' . http_build_query($params);
		
		$request = new CurlRequest();
        
        try {
            $response = $request->setUrl($url)->execute();
        } catch (CurlRequestException $e) {
            LogControl::get_instance()->warning("Could not execute curl request $url. Error: " . $e->getMessage()
                                                . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                                'hub',
                                                'errors');
            
            return null;
        }
		
		if($response->getStatusCode() !== 200)
		{
			LogControl::get_instance()->warning('Could not fetch user interface URL, invalid response code: '
			                                    . $response->getStatusCode(), 'hub', 'errors');
			
			return null;
		}
		
		$body = json_decode($response->getBody(), true);
		
		if(!is_array($body))
		{
			LogControl::get_instance()->warning('Response body is not an array: ' . $response->getBody(), 'hub',
			                                    'errors');
			
			return null;
		}
		
		if(!array_key_exists('userInterfaceUrl', $body) || empty($body['userInterfaceUrl']))
		{
			LogControl::get_instance()
			          ->warning('Response body does not contain userInterfaceUrl or userInterfaceUrl is empty: '
			                    . $response->getBody(), 'hub', 'errors');
			
			return null;
		}
		
		return $body['userInterfaceUrl'];
	}
	
	
	/**
	 * Displays admin infobox message.
	 *
	 * Use this method to inform uses on integration issues.
	 *
	 * @param string $title   Entry title.
	 * @param string $message Entry message.
	 */
	private function displayInfoboxMessage($title, $message)
	{
		$infoboxService = StaticGXCoreLoader::getService('InfoBox');
		$infoboxMessage = MainFactory::create('InfoBoxMessage');
		
		$languageCode = new LanguageCode(new StringType($_SESSION['language_code']));
		
		$infoboxMessage->setIdentifier(new StringType('test-' . time()))
		               ->setCustomerId(new IdType($_SESSION['customer_id']))
		               ->setMessage(new StringType($message), $languageCode)
		               ->setHeadLine(new StringType($title), $languageCode);
		
		$infoboxService->addMessage($infoboxMessage);
	}
}
