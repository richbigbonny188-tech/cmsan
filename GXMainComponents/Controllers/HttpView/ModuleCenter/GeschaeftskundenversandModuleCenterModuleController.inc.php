<?php
/* --------------------------------------------------------------
	GeschaeftskundenversandModuleCenterModuleController.inc.php 2022-08-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2021 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class GeschaeftskundenversandModuleCenterModuleController
 *
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class GeschaeftskundenversandModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * @var GeschaeftskundenversandConfigurationStorage
     */
    protected $configuration;
    
    
    protected function _init()
    {
        $this->pageTitle     = $this->languageTextManager->get_text('geschaeftskundenversand_title');
        $this->configuration = MainFactory::create('GeschaeftskundenversandConfigurationStorage');
    }
    
    
    public function actionDefault()
    {
        $title    = new NonEmptyStringType($this->languageTextManager->get_text('geschaeftskundenversand_title'));
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/module_center/geschaeftskundenversand_configuration.html'));
        
        $productNames    = [];
        $allProductTypes = array_merge(GeschaeftskundenversandProduct::getValidTypes(),
                                       GeschaeftskundenversandProduct::getDeprecatedTypes());
        foreach ($allProductTypes as $productType) {
            $productNames[$productType] = $this->languageTextManager->get_text('gkv_product_' . $productType);
        }
        
        $parcelServiceReader = MainFactory::create('ParcelServiceReader');
        $parcelServices      = $parcelServiceReader->getAllParcelServices();
        
        $products = $this->configuration->getProducts();
        if (count($products) === 0) {
            isset($GLOBALS['messageStack']) or $GLOBALS['messageStack'] = new messageStack();
            $GLOBALS['messageStack']->add($this->languageTextManager->get_text('gkv_warning_no_products'), 'warning');
        }
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'pageToken'                    => $_SESSION['coo_page_token']->generate_token(),
                                        'configuration'                => $this->configuration->get_all(),
                                        'configuration_tree'           => $this->configuration->get_all_tree(),
                                        'action_save_configuration'    => xtc_href_link('admin.php',
                                                                                        'do=GeschaeftskundenversandModuleCenterModule/SaveConfiguration'),
                                        'action_products'              => xtc_href_link('admin.php',
                                                                                        'do=GeschaeftskundenversandModuleCenterModule/EditProducts'),
                                        'product_types'                => GeschaeftskundenversandProduct::getValidTypes(),
                                        'products'                     => $products,
                                        'product_names'                => $productNames,
                                        'orders_statuses'              => $this->getOrdersStatuses(),
                                        'parcel_services'              => $parcelServices,
                                        'countries'                    => $this->getCountries(),
                                        'shipping_modules'             => $this->getShippingModules(),
                                        'checkout_preferences_modules' => explode(',',
                                                                                  $this->configuration->get('checkout_preferences_modules')),
                                    ]);

        /** @var AssetCollectionInterface $assets */
        $assets            = MainFactory::create('AssetCollection', []);
        /** @var ContentNavigationCollectionInterface $contentNavigation */
        $contentNavigation = MainFactory::create('ContentNavigationCollection', []);
        $contentNavigation->add(new StringType($this->languageTextManager->get_text('gkv_config_tab_title_main')),
                                new StringType('admin.php?do=GeschaeftskundenversandModuleCenterModule'),
                                new BoolType(true));
        $contentNavigation->add(new StringType($this->languageTextManager->get_text('gkv_config_tab_title_returns')),
                                new StringType('admin.php?do=GeschaeftskundenversandModuleCenterModule/ReturnsConfiguration'),
                                new BoolType(false));
    
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets,
                                   $contentNavigation);
    }
    
    
    public function actionReturnsConfiguration()
    {
        $title    = new NonEmptyStringType($this->languageTextManager->get_text('geschaeftskundenversand_title'));
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/module_center/geschaeftskundenversand_returns_configuration.html'));
    
        $parcelServiceReader = MainFactory::create('ParcelServiceReader');
        $parcelServices      = $parcelServiceReader->getAllParcelServices();
    
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'pageToken'                 => $_SESSION['coo_page_token']->generate_token(),
                                        'configuration'             => $this->configuration->get_all(),
                                        'configuration_tree'        => $this->configuration->get_all_tree(),
                                        'action_save_configuration' => xtc_href_link('admin.php',
                                                                                     'do=GeschaeftskundenversandModuleCenterModule/SaveReturnsConfiguration'),
                                        'orders_statuses'           => $this->getOrdersStatuses(),
                                        'countries'                 => $this->getCountries(),
                                        'parcel_services'           => $parcelServices,
                                    ]);
    
        /** @var AssetCollectionInterface $assets */
        $assets            = MainFactory::create('AssetCollection', []);
        /** @var ContentNavigationCollectionInterface $contentNavigation */
        $contentNavigation = MainFactory::create('ContentNavigationCollection', []);
        $contentNavigation->add(new StringType($this->languageTextManager->get_text('gkv_config_tab_title_main')),
                                new StringType('admin.php?do=GeschaeftskundenversandModuleCenterModule'),
                                new BoolType(false));
        $contentNavigation->add(new StringType($this->languageTextManager->get_text('gkv_config_tab_title_returns')),
                                new StringType('admin.php?do=GeschaeftskundenversandModuleCenterModule/ReturnsConfiguration'),
                                new BoolType(true));
    
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets,
                                   $contentNavigation);
    }
    
    public function actionSaveConfiguration()
    {
        $this->_validatePageToken();
        $newConfiguration = $this->_getPostData('configuration');
        foreach ($newConfiguration as $key => $value) {
            try {
                $this->configuration->set($key, $value);
            } catch (InvalidEkpFormatException $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('gkv_invalid_ekp_format'),
                                                      'error');
            }
        }
        $checkoutPreferencesModules = $this->_getPostData('checkout_preferences_modules');
        $enabledCheckoutPreferencesModules = [];
        if (is_array($checkoutPreferencesModules)) {
            foreach($checkoutPreferencesModules as $moduleCode => $isChecked) {
                if((bool)$isChecked) {
                    $enabledCheckoutPreferencesModules[] = $moduleCode;
                }
            }
        }
        $this->configuration->set('checkout_preferences_modules', implode(',', $enabledCheckoutPreferencesModules));
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('gkv_configuration_saved'), 'info');

        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=GeschaeftskundenversandModuleCenterModule'));
    }
    
    
    public function actionSaveReturnsConfiguration()
    {
        $this->_validatePageToken();
        $newConfiguration = $this->_getPostData('configuration');
        foreach ($newConfiguration as $key => $value) {
            try {
                $this->configuration->set($key, $value);
            } catch (InvalidEkpFormatException $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('gkv_invalid_ekp_format'),
                                                      'error');
            }
        }
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('gkv_configuration_saved'), 'info');
    
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php',
                                                 'do=GeschaeftskundenversandModuleCenterModule/ReturnsConfiguration'));
    }
    
    public function actionEditProducts()
    {
        $this->_validatePageToken();
        $deleteProductIndex   = $this->_getPostData('delete_product');
        $addProductType       = $this->_getPostData('add_product_type');
        $addProductAttendance = strtoupper(trim($this->_getPostData('add_product_attendance')));
        $addProductAlias      = (string)$this->_getPostData('add_product_alias');
        
        if ($deleteProductIndex !== null) {
            $this->configuration->deleteProduct((int)$deleteProductIndex);
            $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('gkv_product_deleted'), 'info');
        } else {
            try {
                $this->configuration->addProduct(MainFactory::create('GeschaeftskundenversandProduct',
                                                                     $addProductType,
                                                                     $addProductAttendance,
                                                                     $addProductAlias));
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('gkv_product_added'),
                                                      'info');
            } catch (InvalidGKVAttendanceFormatException $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('gkv_attendance_syntax_error'),
                                                      'error');
            } catch (Exception $e) {
                $errorMessage = sprintf('%s (%s)',
                                        $this->languageTextManager->get_text('gkv_invalid_product_error'),
                                        $e->getMessage());
                $GLOBALS['messageStack']->add_session($errorMessage, 'error');
            }
        }
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=GeschaeftskundenversandModuleCenterModule'));
    }
    
    
    /**
     * Retrieves a array of order statuses (IDs and names as per current session language)
     *
     * @return array
     */
    protected function getOrdersStatuses()
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->where(['language_id' => $_SESSION['languages_id']]);
        $db->order_by('orders_status_name ASC');
        $orders_statuses_query = $db->get('orders_status');
        $orders_statuses       = $orders_statuses_query->result();
        
        return $orders_statuses;
    }
    
    
    protected function getCountries()
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->where(['status' => 1]);
        $countriesQuery = $db->get('countries');
        $countries      = $countriesQuery->result();
        
        return $countries;
    }
    
    protected function getShippingModules(): array
    {
        /** @var ConfigurationStorage $configuration */
        $configuration = MainFactory::create('ConfigurationStorage', 'configuration');
        $moduleShippingInstalled = trim($configuration->get('MODULE_SHIPPING_INSTALLED'));
        if (empty($moduleShippingInstalled)) {
            return [];
        }
        $modulesInstalled = explode(';', $configuration->get('MODULE_SHIPPING_INSTALLED'));
        $shippingModules = [];
        
        foreach ($modulesInstalled as $moduleFileName)
        {
            $moduleCode = basename($moduleFileName, '.php');
            $this->languageTextManager->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/shipping/' . $moduleFileName);
            require_once DIR_FS_CATALOG . 'includes/modules/shipping/' . $moduleFileName;
            $module = new $moduleCode();
            $shippingModules[] = [
                'code' => $module->code,
                'title' => $module->title,
            ];
        }
    
        $shippingModules[] = [
            'code'  => 'free',
            'title' => $this->languageTextManager->get_text('MODULE_SHIPPING_FREE_TEXT_TITLE', 'free'),
        ];
    
        return $shippingModules;
    }
}
