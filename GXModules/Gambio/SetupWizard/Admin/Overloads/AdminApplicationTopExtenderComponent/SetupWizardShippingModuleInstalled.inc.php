<?php
/* --------------------------------------------------------------
 SetupWizardShippingModuleInstalled.php 2022-04-27
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class SetupWizardShippingModuleInstalled
 */
class SetupWizardShippingModuleInstalled extends SetupWizardShippingModuleInstalled_parent
{
    public function proceed()
    {
        parent::proceed();
        
        $requestUri = $_SERVER['REQUEST_URI'];
        $activeScript = explode('/', $requestUri);
        $activeScript = array_pop($activeScript);
        
        $urlParts = explode('&', $activeScript);
        if (array_key_exists('set', $_GET) && $_GET['set'] === 'shipping' && ($_GET['action'] ?? '') === 'install'
            && $urlParts[0] === 'modules.php?set=shipping') {
            (new LegacyShippingStepDoneCommand())->execute();
        }
    }
    
}