<?php
/* --------------------------------------------------------------
   HaendlerbundModuleCenterModule.inc.php 2021-10-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Gambio\Core\Configuration\Services\ConfigurationService;

class HaendlerbundModuleCenterModule extends AbstractModuleCenterModule
{
    
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('haendlerbund_title');
        $this->description = $this->languageTextManager->get_text('haendlerbund_description');
        $this->sortOrder   = 1;
    }
    
    public function install()
    {
        /** @var ConfigurationService $configurationService */
        $configurationService = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $configurationService->save('cronjobs/Haendlerbund/interval', '0 0 * * *');
        $configurationService->save('cronjobs/Haendlerbund/active', 'false');

        parent::install();
    }
    
    public function uninstall()
    {
        /** @var ConfigurationService $configurationService */
        $configurationService = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $configurationService->save('cronjobs/Haendlerbund/active', 'false');
        
        parent::uninstall();
    }
}
