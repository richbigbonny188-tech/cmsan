<?php

/* --------------------------------------------------------------
   AdminAccessGroupFactoryInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessGroupFactoryInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
interface AdminAccessGroupFactoryInterface
{
    /**
     * AdminAccessPermissionFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $queryBuilder);
    
    
    /**
     * Creates an admin access group.
     *
     * @return AdminAccessGroupInterface Group object.
     */
    public function createAdminAccessGroup();
    
    
    /**
     * Creates an admin access group collection.
     *
     * @param array $groupsArray Array of AdminAccessGroupInterface objects.
     *
     * @return AdminAccessGroupCollection Group collection.
     */
    public function createAdminAccessGroupCollection(array $groupsArray);
    
    
    /**
     * Creates an admin access group item.
     *
     * @return AdminAccessGroupItemInterface Group item object.
     */
    public function createAdminAccessGroupItem(
        IntType $groupId,
        NonEmptyStringType $identifier,
        NonEmptyStringType $type
    );
    
    
    /**
     * Creates an admin access group item collection.
     *
     * @param array $groupItemsArray Array of AdminAccessGroupItemInterface objects.
     *
     * @return AdminAccessGroupItemCollection Group item collection.
     */
    public function createAdminAccessGroupItemCollection(array $groupItemsArray);
}
