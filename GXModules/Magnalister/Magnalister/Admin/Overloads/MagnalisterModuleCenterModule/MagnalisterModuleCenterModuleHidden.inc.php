<?php

/* --------------------------------------------------------------
   MagnalisterModuleCenterModuleHidden.inc.php 2024-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class MagnalisterModuleCenterModuleHidden extends MagnalisterModuleCenterModuleHidden_parent
{
    /**
     * Returns the visibility of the module.
     * This module is just visible if already installed.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->isInstalled();
    }
}