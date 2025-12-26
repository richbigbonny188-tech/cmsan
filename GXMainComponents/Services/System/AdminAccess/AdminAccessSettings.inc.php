<?php

/* --------------------------------------------------------------
   AdminAccessSettings.inc.php 2018-01-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessSettings
 *
 * @category   System
 * @package    AdminAccess
 */
class AdminAccessSettings implements AdminAccessSettingsInterface
{
    /**
     * Returns the ID of the main admin.
     *
     * @return int ID of the main admin.
     */
    public function getMainAdminId()
    {
        return 1;
    }
}
