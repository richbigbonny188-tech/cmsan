<?php
/* --------------------------------------------------------------
	InternetMarkeModuleCenterModuleController.inc.php 2023-04-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class InternetMarkeModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    protected $internetMarkeText;
    protected $internetMarkeConfiguration;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    public function actionDefault()
    {
        $userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
        $userId                   = MainFactory::create('IdType', (int)$_SESSION['customer_id']);
        $countries                = [];
        $countries_query          = $this->db->get_where('countries', ['status' => '1']);
        foreach ($countries_query->result() as $country_row) {
            $countries[$country_row->countries_id] = [
                'name' => $country_row->countries_name,
                'iso2' => $country_row->countries_iso_code_2,
                'iso3' => $country_row->countries_iso_code_3,
            ];
        }
        #die(print_r($countries, true));
        
        $onec4a      = new OneClick4Application();
        $pageFormats = $onec4a->retrievePageFormatsList();
        try {
            $contractProducts = $onec4a->retrieveContractProducts();
        } catch (SoapFault $sf) {
            $contractProducts = [];
        }
        $dpProductInfo       = new DPProductInformationService();
        $forceUpdate         = false;
        $productList         = $dpProductInfo->getPPLProductList($forceUpdate);
        $categories          = $onec4a->retrievePublicGalleryCategories();
        $parcelServiceReader = MainFactory::create('ParcelServiceReader');
        
        $formdata = [
            'form_action'               => xtc_href_link('admin.php',
                                                         'do=InternetMarkeModuleCenterModule/SaveConfiguration'),
            'form_action_prodcache'     => xtc_href_link('admin.php',
                                                         'do=InternetMarkeModuleCenterModule/ClearProductsCache'),
            'form_action_prodfavorites' => xtc_href_link('admin.php',
                                                         'do=InternetMarkeModuleCenterModule/SaveProductFavorites'),
            'toslink'                   => xtc_catalog_href_link('GXModules/Gambio/Internetmarke/Admin/Html/AGB_INTERNETMARKE_PORTOKASSE_02.06.2014.pdf'),
            'countries'                 => $countries,
            'products'                  => $productList,
            'ppl_date'                  => $dpProductInfo->getProductListDate(),
            'pageformats'               => $pageFormats,
            'contractProducts'          => $contractProducts,
            'imageCategories'           => $categories,
            'configuration'             => [
                'onec4a_email'                => $this->internetMarkeConfiguration->getStored('oneclick4app/credentials/email'),
                'onec4a_password'             => $this->internetMarkeConfiguration->getStored('oneclick4app/credentials/password'),
                'tos_accepted'                => $this->internetMarkeConfiguration->getStored('oneclick4app/tos_accepted'),
                'sender_company'              => $this->internetMarkeConfiguration->get('oneclick4app/sender/company'),
                'sender_firstname'            => $this->internetMarkeConfiguration->get('oneclick4app/sender/firstname'),
                'sender_lastname'             => $this->internetMarkeConfiguration->get('oneclick4app/sender/lastname'),
                'sender_street'               => $this->internetMarkeConfiguration->get('oneclick4app/sender/street'),
                'sender_houseno'              => $this->internetMarkeConfiguration->get('oneclick4app/sender/houseno'),
                'sender_zip'                  => $this->internetMarkeConfiguration->get('oneclick4app/sender/zip'),
                'sender_city'                 => $this->internetMarkeConfiguration->get('oneclick4app/sender/city'),
                'sender_country'              => $this->internetMarkeConfiguration->get('oneclick4app/sender/country'),
                'prefs_productcode'           => $this->internetMarkeConfiguration->get('oneclick4app/prefs/productcode'),
                'prefs_voucherlayout'         => $this->internetMarkeConfiguration->get('oneclick4app/prefs/voucherlayout'),
                'prefs_pageformat'            => $this->internetMarkeConfiguration->get('oneclick4app/prefs/pageformatid'),
                'prefs_imageid'               => $this->internetMarkeConfiguration->get('oneclick4app/prefs/imageid'),
                'parcelservice_id'            => $this->internetMarkeConfiguration->get('oneclick4app/parcelservice_id'),
                'order_status_after_label'    => $this->internetMarkeConfiguration->get('oneclick4app/order_status_after_label'),
                'notify_customer'             => $this->internetMarkeConfiguration->get('oneclick4app/notify_customer'),
                'low_wallet_balance'          => $this->internetMarkeConfiguration->get('oneclick4app/low_wallet_balance'),
                'show_contract_products_only' => $this->internetMarkeConfiguration->get('oneclick4app/show_contract_products_only'),
                'favorites'                   => $this->internetMarkeConfiguration->get('oneclick4app/favorite_products'),
            ],
            'parcel_services'           => $parcelServiceReader->getAllParcelServices(),
            'orders_statuses'           => $this->getOrdersStatuses(),
        ];
        
        $template    = $this->getTemplateFile('Gambio/Internetmarke/Admin/Html/internetmarke_configuration.html');
        $assetsArray = [
            MainFactory::create('Asset',
                                DIR_WS_CATALOG
                                . 'GXModules/Gambio/Internetmarke/Admin/Javascript/controllers/inetmarke_config_controller.js'),
            MainFactory::create('Asset',
                                DIR_WS_CATALOG . 'GXModules/Gambio/Internetmarke/Admin/Styles/internetmarke.css'),
        ];
        $assets      = MainFactory::create('AssetCollection', $assetsArray);
        $data        = MainFactory::create('KeyValueCollection', $formdata);
        $title       = MainFactory::create('NonEmptyStringType',
                                           $this->internetMarkeText->get_text('configuration_heading'));
        $response    = MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
        
        return $response;
    }
    
    
    /**
     * Retrieves a array of order statuses (ids and names as per current session language)
     * @return array
     */
    protected function getOrdersStatuses()
    {
        $this->db->where(['language_id' => $_SESSION['languages_id']]);
        $this->db->order_by('orders_status_name ASC');
        $orders_statuses_query = $this->db->get('orders_status');
        $orders_statuses       = $orders_statuses_query->result();
        
        return $orders_statuses;
    }
    
    
    public function actionSaveConfiguration()
    {
        $newConfiguration = $this->_getPostData('configuration');
        if (!isset($newConfiguration['tos_accepted']) || $newConfiguration['tos_accepted'] != '1') {
            $newConfiguration['onec4a_password'] = '';
        }
        $this->internetMarkeConfiguration->set('oneclick4app/credentials/email', $newConfiguration['onec4a_email']);
        $this->internetMarkeConfiguration->set('oneclick4app/credentials/password',
                                               $newConfiguration['onec4a_password']);
        $this->internetMarkeConfiguration->set('oneclick4app/tos_accepted', $newConfiguration['tos_accepted']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/company', $newConfiguration['sender_company']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/firstname', $newConfiguration['sender_firstname']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/lastname', $newConfiguration['sender_lastname']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/street', $newConfiguration['sender_street']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/houseno', $newConfiguration['sender_houseno']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/zip', $newConfiguration['sender_zip']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/city', $newConfiguration['sender_city']);
        $this->internetMarkeConfiguration->set('oneclick4app/sender/country', $newConfiguration['sender_country']);
        $this->internetMarkeConfiguration->set('oneclick4app/prefs/productcode',
                                               $newConfiguration['prefs_productcode']);
        $this->internetMarkeConfiguration->set('oneclick4app/prefs/voucherlayout',
                                               $newConfiguration['prefs_voucherlayout']);
        $this->internetMarkeConfiguration->set('oneclick4app/prefs/pageformatid',
                                               $newConfiguration['prefs_pageformat']);
        $this->internetMarkeConfiguration->set('oneclick4app/prefs/imageid', $newConfiguration['prefs_imageid']);
        $this->internetMarkeConfiguration->set('oneclick4app/parcelservice_id', $newConfiguration['parcelservice_id']);
        $this->internetMarkeConfiguration->set('oneclick4app/order_status_after_label',
                                               $newConfiguration['order_status_after_label']);
        $this->internetMarkeConfiguration->set('oneclick4app/notify_customer', $newConfiguration['notify_customer']);
        $this->internetMarkeConfiguration->set('oneclick4app/low_wallet_balance',
                                               $newConfiguration['low_wallet_balance']);
        $this->internetMarkeConfiguration->set('oneclick4app/show_contract_products_only',
                                               $newConfiguration['show_contract_products_only']);
        $GLOBALS['messageStack']->add_session($this->internetMarkeText->get_text('configuration_saved'), 'info');
        
        return new RedirectHttpControllerResponse(xtc_href_link('admin.php', 'do=InternetMarkeModuleCenterModule'));
    }
    
    
    public function actionClearProductsCache()
    {
        $dpProductInfo    = new DPProductInformationService();
        $forceUpdate      = true;
        $productList      = $dpProductInfo->getPPLProductList($forceUpdate);
        $oneClick4App     = new OneClick4Application();
        $contractProducts = $oneClick4App->retrieveContractProducts($forceUpdate); 
        
        return new RedirectHttpControllerResponse(xtc_href_link('admin.php', 'do=InternetMarkeModuleCenterModule'));
    }
    
    
    public function actionSaveProductFavorites()
    {
        $favorites = (array)$this->_getPostData('favorites');
        $this->internetMarkeConfiguration->set('oneclick4app/favorite_products', $favorites);
        
        return new RedirectHttpControllerResponse(xtc_href_link('admin.php', 'do=InternetMarkeModuleCenterModule'));
    }
    
    
    protected function _init()
    {
        $this->db                         = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->internetMarkeText          = MainFactory::create('InternetMarkeText');
        $this->internetMarkeConfiguration = MainFactory::create('InternetMarkeConfigurationStorage');
    }
}
