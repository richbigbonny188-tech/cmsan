<?php
/* --------------------------------------------------------------
   AccessGroupWriteService.php 2021-04-07
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
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroupIds;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\DeletionOfAccessGroupsFailedException;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\StorageOfAccessGroupsFailedException;

/**
 * Interface AccessGroupWriteService
 *
 * @package Gambio\Admin\Modules\AccessGroup\Services
 */
interface AccessGroupWriteService
{
    /**
     * Creates a new access group based on the provided names, descriptions, sort order and protection status.
     * The provided names and description arrays need to map language ID (key) and name or description (value).
     *
     * @param array<int, string> $names
     * @param array<int, string> $descriptions
     * @param int                $sortOrder
     * @param bool               $isProtected
     * @param int|null           $parentGroupId Provide null, if there is no parent group.
     *
     * @return AccessGroup
     */
    public function createAccessGroup(
        array $names,
        array $descriptions,
        int $sortOrder,
        bool $isProtected = false,
        ?int $parentGroupId = null
    ): AccessGroup;
    
    
    /**
     * Stores (updates) all provided access groups and returns their group IDs.
     *
     * @param AccessGroup ...$groups
     *
     * @return AccessGroupIds
     *
     * @throws StorageOfAccessGroupsFailedException
     */
    public function storeAccessGroups(AccessGroup ...$groups): AccessGroupIds;
    
    
    /**
     * Deletes all access groups based on the provided group IDs.
     *
     * @param int[] $groupIds
     *
     * @throws DeletionOfAccessGroupsFailedException
     */
    public function deleteAccessGroups(int ...$groupIds): void;
}