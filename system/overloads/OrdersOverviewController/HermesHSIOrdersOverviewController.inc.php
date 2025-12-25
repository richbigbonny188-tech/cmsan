<?php
/* --------------------------------------------------------------
   HermesHSIOrdersOverviewController.inc.php 2019-10-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSIOrdersOverviewController extends HermesHSIOrdersOverviewController_parent
{
    protected function _getAssetsArray()
    {
        $assets = parent::_getAssetsArray();
        $isInstalled = (bool)gm_get_conf('MODULE_CENTER_HERMESHSI_INSTALLED') === true;
        if ($isInstalled) {
            $assets[] = MainFactory::create('Asset', 'module_center_module.lang.inc.php');
            $assets[] = MainFactory::create('Asset',
                                            DIR_WS_CATALOG
                                            . 'admin/html/assets/javascript/modules/hermeshsi/hermeshsi_ordersoverview.min.js');
        }
        
        return $assets;
    }
}
