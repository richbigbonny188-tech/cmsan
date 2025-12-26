<?php

/* --------------------------------------------------------------
   AdminAccessUserFactoryInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessUserFactoryInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
interface AdminAccessUserFactoryInterface
{
    /**
     * AdminAccessUserFactory constructor.
     *
     * @param CI_DB_query_builder    $queryBuilder Query builder.
     * @param AdminAccessRoleFactory $roleFactory  Role factory.
     */
    public function __construct(CI_DB_query_builder $queryBuilder, AdminAccessRoleFactory $roleFactory);
    
    
    /**
     * Creates an admin access user.
     *
     * @param IdType $customerId User id.
     *
     * @return AdminAccessUserInterface User object.
     */
    public function createAdminAccessUser(IdType $customerId);
}
