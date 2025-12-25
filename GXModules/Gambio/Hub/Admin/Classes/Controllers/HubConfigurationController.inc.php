<?php
/* --------------------------------------------------------------
   HubConfigurationController.inc.php 2017-03-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HubConfigurationController
 *
 * This controller contains functionality related to the configuration pages of the Gambio Hub module. It must
 * be used for newer shop installations starting from v3.1.x.x.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class HubConfigurationController extends AdminHttpViewController
{
    /**
     * @var string
     */
    protected $scriptsBaseUrl;
    
    /**
     * @var string
     */
    protected $jsBaseUrl;
    
    /**
     * @var string
     */
    protected $jsControllersUrl;
    
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
	 * @var \HubAssetHelper
	 */
	protected   $hubAssetHelper;
	
	
	/**
     * Initialize the controller.
     */
    public function init()
    {
        if (isset($_SESSION['coo_page_token']) == false) {
            throw new RuntimeException('Page Token Generator not found.'); // CSRF Protection
        }
        $installedVersion = gm_get_conf('INSTALLED_VERSION');
        $this->hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);

        $this->scriptsBaseUrl      = DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl();
        $this->jsBaseUrl           = $this->scriptsBaseUrl . '/vendor';
        $this->jsControllersUrl    = $this->scriptsBaseUrl . '/controllers';
        $this->stylesBaseUrl       = DIR_WS_CATALOG . $this->hubAssetHelper->getStylesBaseUrl();
        $this->minPostfix          = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
        $this->languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_account',
            $_SESSION['languages_id']);
    }
    
    
    /**
     * Display the Gambio Hub account page.
     *
     * Admins are able to register for a new Hub account or handle their existing account information.
     */
    public function actionAccount()
    {
        $title = new NonEmptyStringType($this->languageTextManager->get_text('PAGE_TITLE', 'gambio_hub_account'));
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_CATALOG
                                                            . $this->hubAssetHelper->getTemplatesBasePath() . '/account.html'));
        
        $data = MainFactory::create('KeyValueCollection', [
            'iframe_source_url' => MODULE_PAYMENT_GAMBIO_HUB_ACCOUNT_APP_URL . '?language=' . $_SESSION['language_code']
        ]);
        
        $assetsArray = [
            MainFactory::create('Asset', $this->jsBaseUrl . '/promise' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->jsBaseUrl . '/fetch' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->jsBaseUrl . '/iframe_resizer' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->jsControllersUrl . '/account' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->stylesBaseUrl . '/gambio_hub' . $this->minPostfix . '.css')
        ];
        
        $assets = MainFactory::create('AssetCollection', $assetsArray);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    /**
     * Display the Gambio Hub payment methods page.
     *
     * Admins are able to install/uninstall and configure their own Hub payment methods.
     */
    public function actionPaymentMethods()
    {
        $title = new NonEmptyStringType($this->languageTextManager->get_text('PAGE_TITLE',
            'gambio_hub_payment_methods'));
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_CATALOG
                                                            . $this->hubAssetHelper->getTemplatesBasePath() . '/payment_methods.html'));
        
        $data    = MainFactory::create('KeyValueCollection', [
            'payment_methods_iframe_source_url' => MODULE_PAYMENT_GAMBIO_HUB_SETTINGS_APP_URL
                                                   . '?section=payment-modules&language=' . $_SESSION['language_code'],
            'account_iframe_source_url'         => MODULE_PAYMENT_GAMBIO_HUB_ACCOUNT_APP_URL . '?language='
                                                   . $_SESSION['language_code'],
        ]);
        
        $assetsArray = [
            MainFactory::create('Asset', $this->jsBaseUrl . '/promise' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->jsBaseUrl . '/fetch' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->jsBaseUrl . '/iframe_resizer' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->jsControllersUrl . '/payment_methods' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->jsControllersUrl . '/account' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->stylesBaseUrl . '/gambio_hub' . $this->minPostfix . '.css')
        ];
        
        $assets = MainFactory::create('AssetCollection', $assetsArray);
        
        $contentNavigation = MainFactory::create('ContentNavigationCollection', []);
        
        $contentNavigation->add(new StringType($this->languageTextManager->get_text('TAB_PAYMENT_HUB',
            'gambio_hub_payment_methods')), new StringType('admin.php?do=HubConfiguration/PaymentMethods'),
            new BoolType(true));
        
        $contentNavigation->add(new StringType($this->languageTextManager->get_text('TAB_PAYMENT_MISC',
            'gambio_hub_payment_methods')), new StringType('modules.php?set=payment'), new BoolType(false));
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets,
            $contentNavigation);
    }
    
    
    /**
     * Display the Gambio Hub transactions page.
     *
     * Admins are able to view their Hub transactions.
     *
     * @todo This page is not completely ready yet and hence shall not be displayed.
     */
    public function actionTransactions()
    {
        $title = new NonEmptyStringType($this->languageTextManager->get_text('PAGE_TITLE', 'gambio_hub_transactions'));
        
        $assetsArray = [
            MainFactory::create('Asset', $this->jsBaseUrl . '/promise.js'),
            MainFactory::create('Asset', $this->jsBaseUrl . '/fetch.js'),
            MainFactory::create('Asset', $this->jsBaseUrl . '/iframe_resizer.js'),
            MainFactory::create('Asset', $this->jsControllersUrl . '/transactions' . $this->minPostfix . '.js'),
            MainFactory::create('Asset', $this->stylesBaseUrl . '/gambio_hub' . $this->minPostfix . '.css')
        ];
        
        $assets = MainFactory::create('AssetCollection', $assetsArray);
        
        $html = '<div id="iframe-container"></div>';
        
        return MainFactory::create('AdminPageHttpControllerResponse', $title->asString(), $html, $assets);
    }
}