<?php
/* --------------------------------------------------------------
   CheckoutLoadingSpinnerModuleCenterModuleController.inc.php 2018-04-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the checkout loading spinner module center module controller
 */
class CheckoutLoadingSpinnerModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * Configuration storage
     * @var CheckoutLoadingSpinnerConfigurationStorage
     */
    protected $configuration;
    
    
    /**
     * Initialize
     */
    protected function _init()
    {
        $this->pageTitle     = $this->languageTextManager->get_text('checkout_loading_spinner_title');
        $this->configuration = MainFactory::create('CheckoutLoadingSpinnerConfigurationStorage');
    }
    
    
    /**
     * Return the default page
     * @return AdminLayoutHttpControllerResponse Layout response
     */
    public function actionDefault()
    {
        $title    = new NonEmptyStringType($this->pageTitle);
        $template = $this->getTemplateFile('GXModules/Gambio/CheckoutLoadingSpinner/Admin/Html/checkout_loading_spinner_configuration.html');
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'pageToken'                 => $_SESSION['coo_page_token']->generate_token(),
                                        'configuration'             => $this->configuration->getAll(),
                                        'action_save_configuration' => xtc_href_link('admin.php',
                                                                                     'do=CheckoutLoadingSpinnerModuleCenterModule/Save'),
                                    ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data);
    }
    
    
    /**
     * Save the configuration values
     * @return RedirectHttpControllerResponse Redirect response
     */
    public function actionSave()
    {
        $this->_validatePageToken();
        
        $configuration = $this->_getPostData('configuration');
        
        foreach ($configuration as $key => $value) {
            try {
                $this->configuration->set($key, $value);
            } catch (Exception $exception) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('checkout_loading_spinner_error_saving_configuration'),
                                                      'error');
            }
        }
        
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('checkout_loading_spinner_saved'),
                                              'info');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=CheckoutLoadingSpinnerModuleCenterModule'));
    }
}