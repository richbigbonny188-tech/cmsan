<?php
/* --------------------------------------------------------------
   AdminFactory.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\Services;

use Gambio\Admin\Modules\Admin\Model\Admin;
use Gambio\Admin\Modules\Admin\Model\Collections\AdminIds;
use Gambio\Admin\Modules\Admin\Model\Collections\Admins;
use Gambio\Admin\Modules\Admin\Model\Collections\RoleIds;
use Gambio\Admin\Modules\Admin\Model\ValueObjects\AdminId;
use Gambio\Admin\Modules\Admin\Model\ValueObjects\RoleId;

/**
 * Class AdminFactory
 *
 * @package Gambio\Admin\Modules\Admin\Services
 */
class AdminFactory
{
    /**
     * Creates and returns an admin.
     *
     * @param int    $id
     * @param string $firstName
     * @param string $lastName
     * @param array  $assignedRoleIds
     *
     * @return Admin
     */
    public function createAdmin(int $id, string $firstName, string $lastName, array $assignedRoleIds): Admin
    {
        $roleIds = $this->createRoleIds(...array_map([$this, 'createRoleId'], $assignedRoleIds));
        
        return Admin::create($this->createAdminId($id),
                             $firstName,
                             $lastName,
                             $roleIds);
    }
    
    
    /**
     * Creates and returns a collection of admins.
     *
     * @param Admin ...$admins
     *
     * @return Admins
     */
    public function createAdmins(Admin ...$admins): Admins
    {
        return Admins::create(...$admins);
    }
    
    
    /**
     * Creates and returns an admin ID.
     *
     * @param int $id
     *
     * @return AdminId
     */
    public function createAdminId(int $id): AdminId
    {
        return AdminId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of admin IDs.
     *
     * @param AdminId ...$ids
     *
     * @return AdminIds
     */
    public function createAdminIds(AdminId ...$ids): AdminIds
    {
        return AdminIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns a role ID.
     *
     * @param int $id
     *
     * @return RoleId
     */
    public function createRoleId(int $id): RoleId
    {
        return RoleId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of role IDs.
     *
     * @param RoleId ...$ids
     *
     * @return RoleIds
     */
    public function createRoleIds(RoleId ...$ids): RoleIds
    {
        return RoleIds::create(...$ids);
    }
}