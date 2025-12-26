<?php

/* --------------------------------------------------------------
   AdminAccessServiceFactoryInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessServiceFactoryInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
interface AdminAccessServiceFactoryInterface
{
    /**
     * Creates an admin access service.
     *
     * @return AdminAccessServiceInterface Service object.
     */
    public function createAdminAccessService();
}
