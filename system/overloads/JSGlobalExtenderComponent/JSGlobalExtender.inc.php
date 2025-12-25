<?php
/* --------------------------------------------------------------
   JSGlobalExtender.inc.php 2020-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSGlobalExtender extends JSGlobalExtender_parent
{
    function proceed()
    {
        parent::proceed();

        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_shop_scripts.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/plugins/hoverIntent/hoverIntent.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/plugins/jquery.form.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/functions.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMLightBox.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_shopping_cart.js'));

        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMLiveSearch.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMProductImages.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_form_styles.js'));
        

        if (gm_get_conf('GM_SHOW_FLYOVER') == '1') {
            include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMMegaFlyOver.js'));
        }
    }
}
