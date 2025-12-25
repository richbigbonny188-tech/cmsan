<?php
/* --------------------------------------------------------------
   Permissions.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\AccessRole\Model\Entities\Permission;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\GroupId;
use IteratorAggregate;
use Traversable;

/**
 * Class Permissions
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Collections
 */
class Permissions implements IteratorAggregate
{
    /**
     * @var Permission[]
     */
    private $permissions;
    
    
    /**
     * Permission constructor.
     *
     * @param Permission ...$permissions
     */
    private function __construct(Permission ...$permissions)
    {
        $this->permissions = [];
        foreach ($permissions as $permission) {
            $this->permissions[$permission->groupId()] = $permission;
        }
    }
    
    
    /**
     * @param Permission ...$permissions
     *
     * @return Permissions
     */
    public static function create(Permission ...$permissions): Permissions
    {
        return new self(...$permissions);
    }
    
    
    /**
     * @return Traversable|Permission[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->permissions);
    }
    
    
    /**
     * @param Permission $permission
     *
     * @return Permissions
     */
    public function updatePermission(Permission $permission): Permissions
    {
        $permissions                         = $this->permissions;
        $permissions[$permission->groupId()] = $permission;
        
        return new self(...$permissions);
    }
    
    
    /**
     * @param GroupId $groupId
     *
     * @return Permission|null
     */
    public function getPermissionByGroupId(GroupId $groupId): ?Permission
    {
        return $this->permissions[$groupId->value()] ?? null;
    }
}