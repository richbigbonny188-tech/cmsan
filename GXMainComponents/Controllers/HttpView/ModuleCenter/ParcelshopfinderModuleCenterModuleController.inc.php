<?php
/* --------------------------------------------------------------
	ParcelshopfinderModuleCenterModuleController.inc.php 2017-04-06
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ParcelshopfinderModuleCenterModuleController
 *
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class ParcelshopfinderModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    protected $text;
    protected $configuration;
    
    
    protected function _init()
    {
        $this->text          = MainFactory::create('LanguageTextManager',
                                                   'parcelshopfinder',
                                                   $_SESSION['languages_id']);
        $this->pageTitle     = $this->text->get_text('module_title');
        $this->configuration = MainFactory::create('ConfigurationStorage', 'modules/shipping/parcelshopfinder');
    }
    
    
    public function actionDefault()
    {
        $title          = new NonEmptyStringType($this->text->get_text('configuration_heading'));
        $template       = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                  . '/html/content/module_center/parcelshopfinder_configuration.html'));
        $maxListEntries = $this->configuration->get('maximum_list_entries');
        if (!is_numeric($maxListEntries)) {
            $maxListEntries = 10;
        }
        $data   = MainFactory::create('KeyValueCollection',
                                      [
                                          'form_action'                 => xtc_href_link('admin.php',
                                                                                         'do=ParcelshopfinderModuleCenterModule/SaveConfiguration'),
                                          'google_api_key'              => $this->configuration->get('google_api_key'),
                                          'google_url_signature_secret' => $this->configuration->get('google_url_signature_secret'),
                                          'google_map_type'             => $this->configuration->get('google_map_type'),
                                          'maximum_list_entries'        => $maxListEntries,
                                      ]);
        $assets = MainFactory::create('AssetCollection',
                                      [
                                          MainFactory::create('Asset', 'parcelshopfinder.lang.inc.php'),
                                      ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    public function actionSaveConfiguration()
    {
        $google_api_key = strip_tags(trim($this->_getPostData('google_api_key')));
        $this->configuration->set('google_api_key', $google_api_key);
        
        $google_url_signature_secret = strip_tags(trim($this->_getPostData('google_url_signature_secret')));
        $this->configuration->set('google_url_signature_secret', $google_url_signature_secret);
        
        $google_map_type = $this->_getPostData('google_map_type');
        $google_map_type = in_array($google_map_type, ['none', 'static', 'dynamic']) ? $google_map_type : 'none';
        $this->configuration->set('google_map_type', $google_map_type);
        
        $this->configuration->set('maximum_list_entries', max(10, (int)$this->_getPostData('maximum_list_entries')));
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=ParcelshopfinderModuleCenterModule'));
    }
}
