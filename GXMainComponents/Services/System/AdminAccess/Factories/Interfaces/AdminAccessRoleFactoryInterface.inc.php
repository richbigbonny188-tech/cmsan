<?php

/* --------------------------------------------------------------
   AdminAccessRoleFactoryInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessRoleFactoryInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
interface AdminAccessRoleFactoryInterface
{
    /**
     * Creates an admin access role.
     *
     * @return AdminAccessRoleInterface Role object.
     */
    public function createAdminAccessRole();
    
    
    /**
     * Creates an admin access role collection.
     *
     * @return AdminAccessRoleCollection Role collection.
     */
    public function createAdminAccessRoleCollection();
    
}
