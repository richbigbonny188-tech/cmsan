<?php

/* --------------------------------------------------------------
   AuthFactoryInterface.inc.php 2016-08-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Interface AuthFactoryInterface
 *
 * @category   System
 * @package    Authentication
 * @subpackage Interfaces
 */
interface AuthFactoryInterface
{
    
    /**
     * Creates a AuthService which is used to provide the authentication methods.
     *
     * @return AuthService
     */
    public function createAuthService();
}