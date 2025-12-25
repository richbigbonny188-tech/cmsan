<?php
/* --------------------------------------------------------------
   GambioStoreTokenGenerator.inc.php 2020-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioStoreTokenGenerator
 *
 * Generates the Gambio Store Token for the Shop
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class GambioStoreTokenGenerator
{
    public function generateToken()
    {
        
        $prefix    = 'STORE';
        $date      = date('Ymd');
        $hash      = bin2hex(random_bytes(16));
        $suffix    = 'XX';
        $delimiter = '-';
        
        return $prefix . $delimiter . $date . $delimiter . $hash . $delimiter . $suffix;
    }
}
