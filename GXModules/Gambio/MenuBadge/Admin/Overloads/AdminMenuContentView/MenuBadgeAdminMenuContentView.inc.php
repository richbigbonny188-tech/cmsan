<?php
/* --------------------------------------------------------------
   MenuBadgeAdminMenuContentView.inc.php 2023-02-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class MenuBadgeAdminMenuContentView
 *
 * Add JS for displaying the count of available Gambio Store updates in the Gambio Admin menu.
 */
class MenuBadgeAdminMenuContentView extends MenuBadgeAdminMenuContentView_parent
{
    /**
     * @return string
     */
    public function get_html()
    {
        if (file_exists(DIR_FS_CATALOG . '.dev-environment')) {
            $js = '<script async
                        type="application/javascript"
                        id="gambio-store-updates-badge-js"
                        data-devmode
                        data-shop-url="' . GM_HTTP_SERVER . DIR_WS_CATALOG . '"
                        src="' . GM_HTTP_SERVER . DIR_WS_CATALOG . 'GXModules/Gambio/MenuBadge/Build/Admin/Javascript/gambio_store_updates_badge.js"
                    ></script>';
        } else {
            $js = '<script async
                        type="application/javascript"
                        id="gambio-store-updates-badge-js"
                        data-shop-url="' . GM_HTTP_SERVER . DIR_WS_CATALOG . '"
                        src="' . GM_HTTP_SERVER . DIR_WS_CATALOG . 'GXModules/Gambio/MenuBadge/Build/Admin/Javascript/gambio_store_updates_badge.min.js"
                    ></script>';
        }
        
        return parent::get_html() . $js;
    }
}
