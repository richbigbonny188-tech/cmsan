<?php

/* --------------------------------------------------------------
   KlarnaHubOrdersOverviewController.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class NoPayHubOrdersOverviewController extends NoPayHubOrdersOverviewController_parent
{
    /**
     * Changes all payment status from gambio hub open to not validated.
     */
    public function init()
    {
        parent::init();
        
        MainFactory::create('HubNoPayOrderStatusChanger')->changeStatus();
    }
}