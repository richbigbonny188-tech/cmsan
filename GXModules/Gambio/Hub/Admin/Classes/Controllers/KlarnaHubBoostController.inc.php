<?php
/* --------------------------------------------------------------
   KlarnaHubBoostController.inc.php 2019-03-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Exceptions\CurlRequestException;

/**
 * Class KlarnaHubBoostController
 *
 * Handles KlarnaHub Boost specific operations that cannot be served by generic controllers.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class KlarnaHubBoostController extends AdminHttpViewController
{
    /**
     * @var string
     */
    protected $scriptsUrl;
    
    /**
     * @var string
     */
    protected $vendorUrl;
    
    /**
     * @var string
     */
    protected $controllersUrl;
    
    /**
     * @var string
     */
    protected $stylesUrl;
    
    /**
     * @var string
     */
    protected $postfix;
    
    /**
     * @var \HubAssetHelper
     */
    protected $hubAssetHelper;
    
    /**
     * @var \HubPublic\ValueObjects\HubClientKey
     */
    protected $clientKey;
    
    
    /**
     * Initialize the controller.
     */
    public function init()
    {
        if (isset($_SESSION['coo_page_token']) == false) {
            throw new RuntimeException('Page Token Generator not found.'); // CSRF Protection
        }
        
        $installedVersion     = gm_get_conf('INSTALLED_VERSION');
        $this->hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);
        $this->scriptsUrl     = DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl();
        $this->vendorUrl      = $this->scriptsUrl . '/vendor';
        $this->controllersUrl = $this->scriptsUrl . '/controllers';
        $this->stylesUrl      = DIR_WS_CATALOG . $this->hubAssetHelper->getStylesBaseUrl();
        $this->postfix        = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
        
        try {
            $clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
            
            $this->clientKey = $clientKeyConfiguration->getClientKey();
        } catch (Exception $exception) {
            // Continue without a client key.
        }
    }
    
    
    /**
     * Default Controller Action
     *
     * Renders the KlarnaHub Boost iframe.
     *
     * @return \AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        $title = new NonEmptyStringType('Klarna Boost');
        
        $path = DIR_FS_CATALOG . $this->hubAssetHelper->getTemplatesBasePath() . '/klarna_boost.html';
        
        $template = new ExistingFile(new NonEmptyStringType($path));
        
        $queryParams = [
            'source'      => 'boost',
            'action'      => 'boost-widget-content',
            'language'    => $_SESSION['language_code'],
            'session_key' => $this->_retrieveSessionKey()
        ];
        
        $iframeSourceUrl = MODULE_PAYMENT_GAMBIO_HUB_URL . '/payment_modules/KlarnaHub/callback?'
                           . http_build_query($queryParams);
        
        $data = MainFactory::create('KeyValueCollection', [
            'iframe_source_url' => $iframeSourceUrl
        ]);
        
        $assetsArray = [
            MainFactory::create('Asset', $this->vendorUrl . '/promise' . $this->postfix . '.js'),
            MainFactory::create('Asset', $this->vendorUrl . '/fetch' . $this->postfix . '.js'),
            MainFactory::create('Asset', $this->vendorUrl . '/iframe_resizer' . $this->postfix . '.js'),
            MainFactory::create('Asset', $this->stylesUrl . '/gambio_hub' . $this->postfix . '.css')
        ];
        
        $assets = MainFactory::create('AssetCollection', $assetsArray);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    /**
     * Retrieves a Hub session key.
     *
     * @return string
     */
    protected function _retrieveSessionKey()
    {
        try {
            $hubApiClientFactory = MainFactory::create('HubApiClientFactory');
            
            $sessionsApiClient = $hubApiClientFactory->createSessionsApiClient();
            
            $authHash = AuthHashCreator::create();
            
            $shopUrl = HTTP_SERVER . DIR_WS_CATALOG;
            
            $languageCode = new LanguageCode(new StringType(strtoupper(DEFAULT_LANGUAGE)));
            
            $sessionKey = $sessionsApiClient->startSession($authHash, $shopUrl, $languageCode);
        } catch (UnexpectedValueException $exception) {
            return null;
        } catch (CurlRequestException $exception) {
            return null;
        }
        
        return $sessionKey;
    }
}
