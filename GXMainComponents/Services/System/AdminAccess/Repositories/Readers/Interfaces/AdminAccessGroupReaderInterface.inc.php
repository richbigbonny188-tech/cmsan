<?php

/* --------------------------------------------------------------
  AdminAccessGroupReaderInterface.inc.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface AdminAccessGroupReaderInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
interface AdminAccessGroupReaderInterface
{
    /**
     * Returns an AdminAccessGroup instance by the given AccessGroup ID.
     *
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    public function getById(IdType $groupId);
    
    
    /**
     * Returns an AdminAccessGroup instance by the given group identifier.
     *
     * @param NonEmptyStringType $pageIdentifier Page Identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    public function getByPage(NonEmptyStringType $pageIdentifier);
    
    
    /**
     * Returns an AdminAccessGroup instance by the given ajax handler identifier.
     *
     * @param NonEmptyStringType $identifier Identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    public function getByAjaxHandler(NonEmptyStringType $identifier);
    
    
    /**
     * Returns an AdminAccessGroup instance by the given group identifier.
     *
     * @param NonEmptyStringType $controllerIdentifier Controller identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    public function getByController(NonEmptyStringType $controllerIdentifier);
    
    
    /**
     * Returns an AdminAccessGroupCollection with all existing AdminAccessGroup objects.
     *
     * @return AdminAccessGroupCollection Group collection with all available groups.
     */
    public function getAll();
    
    
    /**
     * Returns an AdminAccessGroupCollection instance with all child groups for the given group ID.
     *
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessGroupCollection Group collection.
     */
    public function getChildren(IdType $groupId);
}
