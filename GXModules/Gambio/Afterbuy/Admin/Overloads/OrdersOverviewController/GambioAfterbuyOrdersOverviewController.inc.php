<?php
/* --------------------------------------------------------------
   GambioAfterbuyOrdersOverviewController.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class GambioAfterbuyOrdersOverviewController
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Overloads\OrdersOverviewController
 */
class GambioAfterbuyOrdersOverviewController extends GambioAfterbuyOrdersOverviewController_parent
{
    /**
     * @return mixed
     */
    protected function _getAssetsArray()
    {
        $assets = parent::_getAssetsArray();
        if ((bool)gm_get_conf('MODULE_CENTER_GAMBIOAFTERBUY_INSTALLED') === true) {
            $assets[] = MainFactory::create('Asset', 'afterbuy.lang.inc.php');
            $assets[] = MainFactory::create('Asset',
                                            DIR_WS_CATALOG
                                            . 'GXModules/Gambio/Afterbuy/Build/Admin/Javascript/modules/gambioafterbuy.min.js');
        }
        
        return $assets;
    }
}
