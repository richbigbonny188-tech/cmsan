<?php
/* --------------------------------------------------------------
   HaendlerbundModuleCenterModuleController.inc.php 2021-10-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HaendlerbundModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    
    protected function _init()
    {
        $this->redirectUrl = xtc_href_link('haendlerbund/configuration');
    }
}
