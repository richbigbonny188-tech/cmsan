<?php
/* --------------------------------------------------------------
   AccessRoleWriteService.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\App;

use Gambio\Admin\Modules\AccessRole\App\Data\AccessRoleRepository;
use Gambio\Admin\Modules\AccessRole\Model\AccessRole;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoleIds;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleFactory;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleWriteService as AccessRoleWriteServiceInterface;

/**
 * Class AccessRoleWriteService
 *
 * @package Gambio\Admin\Modules\AccessRole\App
 */
class AccessRoleWriteService implements AccessRoleWriteServiceInterface
{
    /**
     * @var AccessRoleRepository
     */
    private $repository;
    
    /**
     * @var AccessRoleFactory
     */
    private $factory;
    
    
    /**
     * AccessRoleService constructor.
     *
     * @param AccessRoleRepository $repository
     * @param AccessRoleFactory    $factory
     */
    public function __construct(AccessRoleRepository $repository, AccessRoleFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createAccessRole(
        array $names,
        array $descriptions,
        int $sortOrder,
        bool $isProtected = false
    ): AccessRole {
        $id = $this->repository->createAccessRole($this->factory->createAccessRoleNames($names),
                                                  $this->factory->createAccessRoleDescriptions($descriptions),
                                                  $sortOrder,
                                                  $isProtected);
        
        return $this->factory->createAccessRole($id->value(),
                                                $names,
                                                $descriptions,
                                                $this->factory->createPermissions(),
                                                $sortOrder,
                                                $isProtected);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAccessRoles(AccessRole ...$roles): AccessRoleIds
    {
        return $this->repository->storeAccessRoles(...$roles);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAccessRoles(int ...$roleIds): void
    {
        $ids = array_map([$this->factory, 'createAccessRoleId'], $roleIds);
        
        $this->repository->deleteAccessRoles($this->factory->createAccessRoleIds(...$ids));
    }
}