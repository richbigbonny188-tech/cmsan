<?php
/* --------------------------------------------------------------
   KlarnaOSMModuleCenterModule.inc.php 2022-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaOSMModuleCenterModule  extends AbstractModuleCenterModule
{
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('klarnaosm_title');
        $this->description = $this->languageTextManager->get_text('klarnaosm_description');
        $this->sortOrder   = 76991;
    }
    
    
    /**
     * Installs the module
     */
    public function install()
    {
        parent::install();
    }
}
