<?php
/* --------------------------------------------------------------
   AccessRole.php 2021-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model;

use Gambio\Admin\Modules\AccessRole\Model\Collections\Permissions;
use Gambio\Admin\Modules\AccessRole\Model\Entities\Permission;
use Gambio\Admin\Modules\AccessRole\Model\Events\NamesAndDescriptionsUpdated;
use Gambio\Admin\Modules\AccessRole\Model\Events\PermissionUpdated;
use Gambio\Admin\Modules\AccessRole\Model\Events\SortOrderUpdated;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleDescriptions;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleNames;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\GroupId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\PermissionAction;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class AccessRole
 *
 * @package Gambio\Admin\Modules\AccessRole\Model
 */
class AccessRole extends AbstractEventRaisingEntity
{
    /**
     * @var AccessRoleId
     */
    private $id;
    
    /**
     * @var AccessRoleNames
     */
    private $names;
    
    /**
     * @var AccessRoleDescriptions
     */
    private $descriptions;
    
    /**
     * @var Permissions
     */
    private $permissions;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    /**
     * @var bool
     */
    private $isProtected;
    
    
    /**
     * AccessRole constructor.
     *
     * @param AccessRoleId           $id
     * @param AccessRoleNames        $names
     * @param AccessRoleDescriptions $descriptions
     * @param Permissions            $permissions
     * @param int                    $sortOrder
     * @param bool                   $isProtected
     */
    private function __construct(
        AccessRoleId           $id,
        AccessRoleNames        $names,
        AccessRoleDescriptions $descriptions,
        Permissions            $permissions,
        int                    $sortOrder,
        bool                   $isProtected
    ) {
        $this->id           = $id;
        $this->names        = $names;
        $this->descriptions = $descriptions;
        $this->permissions  = $permissions;
        $this->sortOrder    = $sortOrder;
        $this->isProtected  = $isProtected;
    }
    
    
    /**
     * @param AccessRoleId           $id
     * @param AccessRoleNames        $names
     * @param AccessRoleDescriptions $descriptions
     * @param Permissions            $permissions
     * @param int                    $sortOrder
     * @param bool                   $isProtected
     *
     * @return AccessRole
     */
    public static function create(
        AccessRoleId           $id,
        AccessRoleNames        $names,
        AccessRoleDescriptions $descriptions,
        Permissions            $permissions,
        int                    $sortOrder,
        bool                   $isProtected
    ): AccessRole {
        return new self($id, $names, $descriptions, $permissions, $sortOrder, $isProtected);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function name(string $languageCode): string
    {
        return $this->names->getName($languageCode);
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function description(string $languageCode): string
    {
        return $this->descriptions->getDescription($languageCode);
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * @return Permissions
     */
    public function permissions(): Permissions
    {
        return $this->permissions;
    }
    
    
    /**
     * @return bool
     */
    public function isProtected(): bool
    {
        return $this->isProtected;
    }
    
    
    /**
     * @param PermissionAction $action
     * @param GroupId          $groupId
     *
     * @return bool
     */
    public function checkPermission(PermissionAction $action, GroupId $groupId): bool
    {
        $permission = $this->permissions->getPermissionByGroupId($groupId);
        
        switch ($action->value()) {
            case PermissionAction::READ:
                return $permission !== null && $permission->readingGranted();
            case PermissionAction::WRITE:
                return $permission !== null && $permission->writingGranted();
            case PermissionAction::DELETE:
                return $permission !== null && $permission->deletingGranted();
            default:
                return false;
        }
    }
    
    
    /**
     * @param AccessRoleNames        $names
     * @param AccessRoleDescriptions $descriptions
     */
    public function updateNamesAndDescriptions(
        AccessRoleNames        $names,
        AccessRoleDescriptions $descriptions
    ): void {
        $this->names        = $names;
        $this->descriptions = $descriptions;
        
        $this->raiseEvent(NamesAndDescriptionsUpdated::create($this->id, $names, $descriptions));
    }
    
    
    /**
     * @param int $sortOrder
     */
    public function updateSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
        
        $this->raiseEvent(SortOrderUpdated::create($this->id, $sortOrder));
    }
    
    
    /**
     * @param Permission $permission
     */
    public function updatePermission(Permission $permission): void
    {
        $this->permissions = $this->permissions->updatePermission($permission);
        
        $this->raiseEvent(PermissionUpdated::create($this->id, $permission));
    }
}