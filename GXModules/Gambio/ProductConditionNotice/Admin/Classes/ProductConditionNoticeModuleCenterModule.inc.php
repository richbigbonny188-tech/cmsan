<?php
/* --------------------------------------------------------------
   ProductConditionNoticeModuleCenterModule.inc.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeStaticServiceFactory;

/**
 * Class ProductConditionNoticeModuleCenterModule
 */
class ProductConditionNoticeModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * @inheritDoc
     */
    protected function _init()
    {
        $textPhraseService = ProductConditionNoticeStaticServiceFactory::createTextPhraseService();
        
        $this->title       = $textPhraseService->getTextPhrase('module_title');
        $this->description = $textPhraseService->getTextPhrase('module_description');
        $this->sortOrder   = 28476;
    }
    
    
    /**
     * @inheritDoc
     */
    public function install()
    {
        parent::install();
        
        $moduleInstallationService = ProductConditionNoticeStaticServiceFactory::createModuleInstallationService();
        $moduleInstallationService->installModule();
    }
    
    
    /**
     * @inheritDoc
     */
    public function uninstall()
    {
        parent::uninstall();
        
        $moduleInstallationService = ProductConditionNoticeStaticServiceFactory::createModuleInstallationService();
        $moduleInstallationService->uninstallModule();
    }
}