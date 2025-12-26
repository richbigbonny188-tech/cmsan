<?php
/* --------------------------------------------------------------
   AccessRoleReadService.php 2021-04-07
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
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoles;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\AccessRoleDoesNotExistException;

/**
 * Interface AccessRoleReadService
 *
 * @package Gambio\Admin\Modules\AccessRole\Services
 */
interface AccessRoleReadService
{
    /**
     * Returns all available access roles.
     *
     * @return AccessRoles
     */
    public function getAccessRoles(): AccessRoles;
    
    
    /**
     * Returns all access roles assigned to a specific admin, based on the provided admin ID.
     *
     * @param int $adminId
     *
     * @return AccessRoles
     */
    public function getAccessRolesByAdmin(int $adminId): AccessRoles;
    
    
    /**
     * Returns a specific access role based on the provided role ID.
     *
     * @param int $roleId
     *
     * @return AccessRole
     *
     * @throws AccessRoleDoesNotExistException
     */
    public function getAccessRoleById(int $roleId): AccessRole;
}