<?php
/* --------------------------------------------------------------
   CashFlowTechModuleCenterModule.inc.php 2018-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CashFlowTechModuleCenterModule extends AbstractModuleCenterModule
{
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('cashflowtech_mcm_title');
        $this->description = $this->languageTextManager->get_text('cashflowtech_mcm_description');
        $this->sortOrder   = 21180;
    }
    
    
    /**
     * Installs the module (optional)
     */
    public function install()
    {
        parent::install();
    }
    
    /**
     * Uninstalls the module (optional)
     */
    public function uninstall()
    {
        parent::uninstall();
    }
}
