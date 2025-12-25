<?php

/* --------------------------------------------------------------
   SingleSignonModuleCenterModuleController.inc.php 2017-10-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SingleSignonModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * @var SingleSignonConfigurationStorage
     */
    protected $configuration;
    
    
    protected function _init()
    {
        $this->pageTitle     = $this->languageTextManager->get_text('singlesignon_title');
        $this->configuration = MainFactory::create('SingleSignonConfigurationStorage');
    }
    
    
    public function actionDefault()
    {
        $title    = new NonEmptyStringType($this->languageTextManager->get_text('singlesignon_title'));
        $template = $this->getTemplateFile('Gambio/SingleSignOn/Admin/Html/sso_configuration.html');
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'pageToken'                 => $_SESSION['coo_page_token']->generate_token(),
                                        'configuration'             => $this->configuration->get_all(),
                                        'action_save_configuration' => xtc_href_link('admin.php',
                                                                                     'do=SingleSignonModuleCenterModule/SaveConfiguration'),
                                        'debug'                     => print_r($this->configuration->get_all(), true),
                                    ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data);
    }
    
    
    public function actionSaveConfiguration()
    {
        $this->_validatePageToken();
        
        $newConfiguration = $this->_getPostData('configuration');
        foreach ($newConfiguration as $key => $value) {
            try {
                $this->configuration->set($key, $value);
            } catch (Exception $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('sso_error_saving_configuration'),
                                                      'error');
            }
        }
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('sso_configuration_saved'), 'info');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=SingleSignonModuleCenterModule'));
    }
}
