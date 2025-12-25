<?php
/* --------------------------------------------------------------
   JSCheckoutExtender.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSCheckoutExtender extends JSCheckoutExtender_parent
{
    function proceed()
    {
        parent::proceed();

        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/PreserveSessionHandler.js'));

        if (gm_get_conf('GM_LIGHTBOX_CHECKOUT') == 'true') {
            include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMShowLightBox.js'));
        }
    }
}
