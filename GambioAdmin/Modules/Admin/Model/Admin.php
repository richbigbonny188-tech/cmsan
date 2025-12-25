<?php
/* --------------------------------------------------------------
   Admin.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\Model;

use Gambio\Admin\Modules\Admin\Model\Collections\RoleIds;
use Gambio\Admin\Modules\Admin\Model\Events\RoleFromAdminRemoved;
use Gambio\Admin\Modules\Admin\Model\Events\RoleToAdminAssigned;
use Gambio\Admin\Modules\Admin\Model\ValueObjects\AdminId;
use Gambio\Admin\Modules\Admin\Model\ValueObjects\RoleId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class Admin
 *
 * @package Gambio\Admin\Modules\Admin\Model
 */
class Admin extends AbstractEventRaisingEntity
{
    /**
     * @var AdminId
     */
    private $id;
    
    /**
     * @var string
     */
    private $firstName;
    
    /**
     * @var string
     */
    private $lastName;
    
    /**
     * @var RoleIds
     */
    private $assignedRoleIds;
    
    
    /**
     * Admin constructor.
     *
     * @param AdminId $id
     * @param string  $firstName
     * @param string  $lastName
     * @param RoleIds $assignedRoleIds
     */
    private function __construct(AdminId $id, string $firstName, string $lastName, RoleIds $assignedRoleIds)
    {
        $this->id              = $id;
        $this->firstName       = $firstName;
        $this->lastName        = $lastName;
        $this->assignedRoleIds = $assignedRoleIds;
    }
    
    
    /**
     * @param AdminId $id
     * @param string  $firstName
     * @param string  $lastName
     * @param RoleIds $assignedRoleIds
     *
     * @return Admin
     */
    public static function create(
        AdminId $id,
        string $firstName,
        string $lastName,
        RoleIds $assignedRoleIds
    ): Admin {
        return new self($id, $firstName, $lastName, $assignedRoleIds);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }
    
    
    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }
    
    
    /**
     * @return RoleIds
     */
    public function assignedRoleIds(): RoleIds
    {
        return $this->assignedRoleIds;
    }
    
    
    /**
     * @param RoleId $roleId
     */
    public function assignRole(RoleId $roleId): void
    {
        $this->assignedRoleIds = $this->assignedRoleIds->withRoleId($roleId);
        
        $this->raiseEvent(RoleToAdminAssigned::create($this->id, $roleId));
    }
    
    
    /**
     * @param RoleId $roleId
     */
    public function removeRole(RoleId $roleId): void
    {
        $this->assignedRoleIds = $this->assignedRoleIds->withoutRoleId($roleId);
        
        $this->raiseEvent(RoleFromAdminRemoved::create($this->id, $roleId));
    }
}