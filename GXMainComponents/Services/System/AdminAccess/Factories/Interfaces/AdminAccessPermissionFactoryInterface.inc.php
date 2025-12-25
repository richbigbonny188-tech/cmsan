<?php

/* --------------------------------------------------------------
   AdminAccessPermissionFactoryInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionFactoryInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
interface AdminAccessPermissionFactoryInterface
{
    /**
     * AdminAccessPermissionFactory constructor.
     *
     * @param CI_DB_query_builder             $queryBuilder Query builder.
     * @param AdminAccessGroupReaderInterface $groupReader  Admin access group reader.
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        AdminAccessGroupReaderInterface $groupReader
    );
    
    
    /**
     * Creates an admin access permission.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Permission object.
     */
    public function createAdminAccessPermission();
    
    
    /**
     * Creates an admin access permission collection.
     *
     * @param array $permissionObjectsArray Array of permission objects.
     *
     * @return AdminAccessPermissionCollection Permission collection.
     */
    public function createAdminAccessPermissionCollection(array $permissionObjectsArray = []);
}
