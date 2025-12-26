<?php
/* --------------------------------------------------------------
   PermissionUpdated.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\Events;

use Gambio\Admin\Modules\AccessRole\Model\Entities\Permission;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;

/**
 * Class PermissionUpdated
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Events
 * @codeCoverageIgnore
 */
class PermissionUpdated
{
    /**
     * @var AccessRoleId
     */
    private $roleId;
    
    /**
     * @var Permission
     */
    private $permission;
    
    
    /**
     * PermissionUpdated constructor.
     *
     * @param AccessRoleId $roleId
     * @param Permission   $permission
     */
    private function __construct(AccessRoleId $roleId, Permission $permission)
    {
        $this->roleId     = $roleId;
        $this->permission = $permission;
    }
    
    
    /**
     * @param AccessRoleId $roleId
     * @param Permission   $permission
     *
     * @return PermissionUpdated
     */
    public static function create(AccessRoleId $roleId, Permission $permission): PermissionUpdated
    {
        return new self($roleId, $permission);
    }
    
    
    /**
     * @return AccessRoleId
     */
    public function accessRoleId(): AccessRoleId
    {
        return $this->roleId;
    }
    
    
    /**
     * @return Permission
     */
    public function permission(): Permission
    {
        return $this->permission;
    }
}