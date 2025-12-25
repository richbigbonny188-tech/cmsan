<?php
/* --------------------------------------------------------------
   KlarnaOSModuleCenterModuleController.inc.php 2022-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\CookieConsentPanel\Services\Purposes\Entities\Purpose;
use Gambio\CookieConsentPanel\Services\Purposes\PurposeReaderService;

class KlarnaOSMModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    protected function _init()
    {
        $this->pageTitle     = $this->languageTextManager->get_text('klarnaosm_title');
        $this->configuration = MainFactory::create('KlarnaOSMConfigurationStorage');
    }
    
    
    public function actionDefault()
    {
        if (!defined('CURRENT_THEME') || constant('CURRENT_THEME') === '') {
            $GLOBALS['messageStack']->add($this->languageTextManager->get_text('klarnaosm_template_warning'),
                                          'warning');
        }
        
        $purposes = [];
        try {
            /** @var PurposeReaderService $purposeReaderService */
            $purposeReaderService = StaticGXCoreLoader::getService('PurposeReader');
            $allPurposes          = $purposeReaderService->allPurposes();
            /** @var Purpose $purpose */
            foreach ($allPurposes as $purpose) {
                $purposes[] = [
                    'id'   => $purpose->id()->value(),
                    'name' => $purpose->name()->value()[$_SESSION['languages_id']],
                ];
            }
        } catch (\Exception $e) {
        }
        
        $title        = new NonEmptyStringType($this->languageTextManager->get_text('klarnaosm_title'));
        $template     = $this->getTemplateFile('Gambio/KlarnaOSM/Admin/Html/klarnaosm_configuration.html');
        $templateData = [
            'pageToken'                 => $_SESSION['coo_page_token']->generate_token(),
            'configuration'             => $this->configuration->get_all(),
            'purposes'                  => $purposes,
            'action_save_configuration' => xtc_href_link('admin.php',
                                                         'do=KlarnaOSMModuleCenterModule/SaveConfiguration'),
        ];
        $data         = MainFactory::create('KeyValueCollection', $templateData);
        
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
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('error_saving_configuration'),
                                                      'error');
            }
        }
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('klarnaosm_configuration_saved'),
                                              'info');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=KlarnaOSMModuleCenterModule'));
    }
}