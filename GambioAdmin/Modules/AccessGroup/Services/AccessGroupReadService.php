<?php
/* --------------------------------------------------------------
   AccessGroupReadService.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Services;

use Gambio\Admin\Modules\AccessGroup\Model\AccessGroup;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroups;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\AccessGroupDoesNotExistException;

/**
 * Interface AccessGroupReadService
 *
 * @package Gambio\Admin\Modules\AccessGroup\Services
 */
interface AccessGroupReadService
{
    /**
     * Returns all available access groups.
     *
     * @return AccessGroups
     */
    public function getAccessGroups(): AccessGroups;
    
    
    /**
     * Returns a specific access group based on the provided group ID.
     *
     * @param int $groupId
     *
     * @return AccessGroup
     *
     * @throws AccessGroupDoesNotExistException
     */
    public function getAccessGroupById(int $groupId): AccessGroup;
    
    
    /**
     * Returns a specific access group based on the provided group item type and descriptor.
     *
     * @param string $type
     * @param string $descriptor
     *
     * @return AccessGroup
     *
     * @throws AccessGroupDoesNotExistException
     */
    public function getAccessGroupByTypeAndDescriptor(string $type, string $descriptor): AccessGroup;
    
    
    /**
     * Returns the best-matching access group based on the provided group item type and descriptor.
     *
     * If there is no group for a specific route or controller (e.g. `/admin/route/specific` or `controller/action`),
     * then it's possible that a group will be returned, which belongs to a more generic route or controller
     * (e.g. `/admin/route` or `controller`).
     *
     * If absolutely no group matches, this service will return the group for unknown items.
     *
     * @param string $type
     * @param string $descriptor
     *
     * @return AccessGroup
     */
    public function findAccessGroupByTypeAndDescriptor(string $type, string $descriptor): AccessGroup;
    
    
    /**
     * Returns a the access group for unknown items based on the provided group item type.
     *
     * @param string $type
     *
     * @return AccessGroup
     */
    public function getAccessGroupForUnknownItemsByType(string $type): AccessGroup;
}