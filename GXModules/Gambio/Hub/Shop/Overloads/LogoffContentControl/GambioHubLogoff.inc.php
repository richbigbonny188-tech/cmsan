<?php
/* --------------------------------------------------------------
   GambioHubLogoff.inc.php 2017-02-22
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubLogoff
 */
class GambioHubLogoff extends GambioHubLogoff_parent
{
    /**
     * Clears the hub session key from the session
     */
    public function reset_user_session()
    {
        unset($_SESSION['gambio_hub_session_key']);

        parent::reset_user_session();
    }
    
}