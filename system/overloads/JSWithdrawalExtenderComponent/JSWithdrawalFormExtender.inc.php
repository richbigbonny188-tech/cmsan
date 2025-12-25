<?php
/* --------------------------------------------------------------
   JSWithdrawalFormExtender.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSWithdrawalFormExtender extends JSWithdrawalFormExtender_parent
{
    function proceed()
    {
        parent::proceed();

        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/ui/jquery-ui.js'));
        include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/ui/datepicker/jquery-ui-datepicker.js'));
    }
}
