<?php
/* --------------------------------------------------------------
   GiftSystemModuleCenterModule.inc.php 2020-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class GiftSystemModuleCenterModule extends AbstractModuleCenterModule
{
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('giftsystem_title');
        $this->description = $this->languageTextManager->get_text('giftsystem_description');
        $this->sortOrder   = 69127;
    }
    
    public function install()
    {
        parent::install();
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->update('gx_configurations', ['value' => 'true'], ['key' => 'configuration/ACTIVATE_GIFT_SYSTEM']);
    }
    
    public function uninstall()
    {
        parent::uninstall();
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->update('gx_configurations', ['value' => 'false'], ['key' => 'configuration/ACTIVATE_GIFT_SYSTEM']);
    }
}
