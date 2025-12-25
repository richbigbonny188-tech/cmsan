<?php
/* --------------------------------------------------------------
   JSCatExtender.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSCatExtender extends JSCatExtender_parent
{
    function proceed()
    {
        parent::proceed();

        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMOrderQuantityChecker.js'));

        if ($this->get_calculate_price() == true) {
            include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMAttributesCalculator.js'));
        }
    }
}

?>
