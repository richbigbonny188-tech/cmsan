<?php

/* --------------------------------------------------------------
	ResponsiveFileManagerModuleCenterModuleController.inc.php 2017-09-29
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ResponsiveFileManagerModuleCenterModuleController.
 *
 * Class representing the module center module controller for the responsive file manager.
 *
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class ResponsiveFileManagerModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * Configuration storage.
     *
     * @var ResponsiveFileManagerConfigurationStorage
     */
    protected $configurationStorage;
    
    /**
     * Language text manager for the module.
     *
     * @var ResponsiveFileManagerLanguageTextManager
     */
    protected $moduleLanguageTextManager;
    
    
    /**
     * Initialize the module e.g. set title, description, sort order etc.
     *
     * Function will be called in the constructor
     */
    protected function _init()
    {
        $this->configurationStorage      = MainFactory::create('ResponsiveFileManagerConfigurationStorage');
        $this->moduleLanguageTextManager = MainFactory::create('ResponsiveFileManagerLanguageTextManager',
                                                               $_SESSION['languages_id']);
        $this->pageTitle                 = $this->moduleLanguageTextManager->getText('module_title');
    }
    
    
    /**
     * Shows the configuration page.
     *
     * @returns AdminLayoutHttpControllerResponse Default page.
     */
    public function actionDefault()
    {
        /**
         * @var AdminLayoutHttpControllerResponse $httpResponse
         */
        
        $title = new NonEmptyStringType($this->moduleLanguageTextManager->getText('module_title'));
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/module_center/responsivefilemanager_configuration.html'));
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'form_action'                       => xtc_href_link('admin.php',
                                                                                             'do=ResponsiveFileManagerModuleCenterModule/SaveConfiguration'),
                                        'use_in_ckeditor'                   => $this->configurationStorage->get('use_in_ckeditor'),
                                        'use_in_product_and_category_pages' => $this->configurationStorage->get('use_in_product_and_category_pages'),
                                        'use_in_manufacturer_pages'         => $this->configurationStorage->get('use_in_manufacturer_pages'),
                                        'use_in_content_manager_pages'      => $this->configurationStorage->get('use_in_content_manager_pages'),
                                        'use_in_attribute_pages'            => $this->configurationStorage->get('use_in_attribute_pages'),
                                        'use_in_property_pages'             => $this->configurationStorage->get('use_in_property_pages'),
                                        'use_in_banner_manager_pages'       => $this->configurationStorage->get('use_in_banner_manager_pages'),
                                        'use_in_shipping_status_pages'      => $this->configurationStorage->get('use_in_shipping_status_pages'),
                                        'use_in_email_pages'                => $this->configurationStorage->get('use_in_email_pages')
                                    ]);
        
        $assets = MainFactory::create('AssetCollection',
                                      [
                                          MainFactory::create('Asset', 'responsivefilemanager.lang.inc.php'),
                                      ]);
        
        $httpResponse = MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
        
        return $httpResponse;
    }
    
    
    /**
     * Saves the configuration.
     *
     * @returns RedirectHttpControllerResponse Default page.
     */
    public function actionSaveConfiguration()
    {
        $this->configurationStorage->set('use_in_ckeditor', $this->_getPostData('use_in_ckeditor'));
        $this->configurationStorage->set('use_in_product_and_category_pages',
                                         $this->_getPostData('use_in_product_and_category_pages'));
        $this->configurationStorage->set('use_in_manufacturer_pages', $this->_getPostData('use_in_manufacturer_pages'));
        $this->configurationStorage->set('use_in_content_manager_pages',
                                         $this->_getPostData('use_in_content_manager_pages'));
        $this->configurationStorage->set('use_in_attribute_pages', $this->_getPostData('use_in_attribute_pages'));
        $this->configurationStorage->set('use_in_property_pages', $this->_getPostData('use_in_property_pages'));
        $this->configurationStorage->set('use_in_banner_manager_pages',
                                         $this->_getPostData('use_in_banner_manager_pages'));
        $this->configurationStorage->set('use_in_shipping_status_pages',
                                         $this->_getPostData('use_in_shipping_status_pages'));
        $this->configurationStorage->set('use_in_email_pages', $this->_getPostData('use_in_email_pages'));
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=ResponsiveFileManagerModuleCenterModule'));
    }
    
    
    /**
     * Returns the configuration as JSON response.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionGetConfiguration()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   [
                                       'isInstalled'                  => $this->configurationStorage->isInstalled(),
                                       'useInCkeditor'                => $this->configurationStorage->get('use_in_ckeditor')
                                                                         === '1',
                                       'useInProductAndCategoryPages' => $this->configurationStorage->get('use_in_product_and_category_pages')
                                                                         === '1',
                                       'useInManufacturerPages'       => $this->configurationStorage->get('use_in_manufacturer_pages')
                                                                         === '1',
                                       'useInContentManagerPages'     => $this->configurationStorage->get('use_in_content_manager_pages')
                                                                         === '1',
                                       'useInAttributePages'          => $this->configurationStorage->get('use_in_attribute_pages')
                                                                         === '1',
                                       'use_in_property_pages'          => $this->configurationStorage->get('use_in_property_pages')
                                                                         === '1',
                                       'useInBannerManagerPages'      => $this->configurationStorage->get('use_in_banner_manager_pages')
                                                                         === '1',
                                       'useInShippingStatusPages'     => $this->configurationStorage->get('use_in_shipping_status_pages')
                                                                         === '1',
                                       'useInEmailPages'              => $this->configurationStorage->get('use_in_email_pages')
                                                                         === '1'
                                   ]);
    }
}