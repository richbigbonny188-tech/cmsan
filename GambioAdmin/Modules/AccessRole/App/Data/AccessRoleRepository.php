<?php
/* --------------------------------------------------------------
   AccessRoleRepository.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\App\Data;

use Gambio\Admin\Modules\AccessRole\Model\AccessRole;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoleIds;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoles;
use Gambio\Admin\Modules\AccessRole\Model\Events\AccessRoleCreated;
use Gambio\Admin\Modules\AccessRole\Model\Events\AccessRoleDeleted;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleDescriptions;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleNames;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AdminId;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\AccessRoleDoesNotExistException;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\DeletionOfAccessRolesFailedException;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\StorageOfAccessRolesFailedException;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AccessRoleRepository
 *
 * @package Gambio\Admin\Modules\AccessRole\App\Data
 */
class AccessRoleRepository extends AbstractEventDispatchingRepository
{
    /**
     * @var AccessRoleMapper
     */
    private $mapper;
    
    /**
     * @var AccessRoleReader
     */
    private $reader;
    
    /**
     * @var AccessRoleWriter
     */
    private $writer;
    
    
    /**
     * AccessRoleRepository constructor.
     *
     * @param AccessRoleMapper         $mapper
     * @param AccessRoleReader         $reader
     * @param AccessRoleWriter         $writer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AccessRoleMapper $mapper,
        AccessRoleReader $reader,
        AccessRoleWriter $writer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
        
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @return AccessRoles
     */
    public function getAccessRoles(): AccessRoles
    {
        return $this->mapper->mapAccessRoles($this->reader->getAccessRolesData());
    }
    
    
    /**
     * @param AdminId $admin
     *
     * @return AccessRoles
     */
    public function getAccessRolesByAdmin(AdminId $admin): AccessRoles
    {
        return $this->mapper->mapAccessRoles($this->reader->getAccessRolesDataByAdmin($admin));
    }
    
    
    /**
     * @param AccessRoleNames        $names
     * @param AccessRoleDescriptions $descriptions
     * @param int                    $sortOrder
     * @param bool                   $isProtected
     *
     * @return AccessRoleId
     */
    public function createAccessRole(
        AccessRoleNames $names,
        AccessRoleDescriptions $descriptions,
        int $sortOrder,
        bool $isProtected = false
    ): AccessRoleId {
        $role = $this->writer->createAccessRole($names, $descriptions, $sortOrder, $isProtected);
        $id   = $this->mapper->mapAccessRoleId($role);
        
        $this->dispatchEvent(AccessRoleCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @param AccessRoleId $roleId
     *
     * @return AccessRole
     *
     * @throws AccessRoleDoesNotExistException
     */
    public function getAccessRoleById(AccessRoleId $roleId): AccessRole
    {
        return $this->mapper->mapAccessRole($this->reader->getAccessRoleDataById($roleId));
    }
    
    
    /**
     * @param AccessRole ...$roles
     *
     * @return AccessRoleIds
     *
     * @throws StorageOfAccessRolesFailedException
     */
    public function storeAccessRoles(AccessRole ...$roles): AccessRoleIds
    {
        $ids = $this->mapper->mapAccessRoleIds($this->writer->storeAccessRoles(...$roles));
        foreach ($roles as $index => $role) {
            $this->dispatchEntityEvents($role);
        }
        
        return $ids;
    }
    
    
    /**
     * @param AccessRoleIds $roleIds
     *
     * @throws DeletionOfAccessRolesFailedException
     */
    public function deleteAccessRoles(AccessRoleIds $roleIds): void
    {
        $this->writer->deleteAccessRoles($roleIds);
        foreach ($roleIds as $roleId) {
            $this->dispatchEvent(AccessRoleDeleted::create($roleId));
        }
    }
}