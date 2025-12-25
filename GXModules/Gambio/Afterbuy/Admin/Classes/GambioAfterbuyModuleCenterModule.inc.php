<?php
/* --------------------------------------------------------------
   GambioAfterbuyModuleCenterModule.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class GambioAfterbuyModuleCenterModule
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class GambioAfterbuyModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * @return void
     */
    protected function _init(): void
    {
        $this->title       = $this->languageTextManager->get_text('gambioafterbuy_title');
        $this->description = $this->languageTextManager->get_text('gambioafterbuy_description');
        $this->sortOrder   = 0.5;
    }
    
    
    /**
     * Installs the module
     *
     * @return void
     * @throws Exception
     */
    public function install(): void
    {
        parent::install();
        
        $storage        = MainFactory::create(GambioAfterbuyConfigurationStorage::class);
        $configurations = $storage->get_all();
        foreach ($configurations as $key => $value) {
            $storage->set($key, $value);
        }
    }
}
