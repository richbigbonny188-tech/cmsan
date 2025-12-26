<?php
/* --------------------------------------------------------------
   AccessRoleWriteService.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Services;

use Gambio\Admin\Modules\AccessRole\Model\AccessRole;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoleIds;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\DeletionOfAccessRolesFailedException;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\StorageOfAccessRolesFailedException;

/**
 * Interface AccessRoleWriteService
 *
 * @package Gambio\Admin\Modules\AccessRole\Services
 */
interface AccessRoleWriteService
{
    /**
     * Creates a new access role based on the provided names, descriptions, sort order and protection status.
     * The provided names and description arrays need to map language ID (key) and name or description (value).
     *
     * @param array<int, string> $names
     * @param array<int, string> $descriptions
     * @param int                $sortOrder
     * @param bool               $isProtected
     *
     * @return AccessRole
     */
    public function createAccessRole(
        array $names,
        array $descriptions,
        int $sortOrder,
        bool $isProtected = false
    ): AccessRole;
    
    
    /**
     * Stores (creates or updates) all provided access roles and returns their role IDs.
     *
     * @param AccessRole ...$roles
     *
     * @return AccessRoleIds
     *
     * @throws StorageOfAccessRolesFailedException
     */
    public function storeAccessRoles(AccessRole ...$roles): AccessRoleIds;
    
    
    /**
     * Deletes all access roles based on the provided role IDs.
     *
     * @param int[] $roleIds
     *
     * @throws DeletionOfAccessRolesFailedException
     */
    public function deleteAccessRoles(int ...$roleIds): void;
}