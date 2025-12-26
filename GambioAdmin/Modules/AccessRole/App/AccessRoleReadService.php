<?php
/* --------------------------------------------------------------
   AccessRoleReadService.php 2020-10-21
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
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoles;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleFactory;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleReadService as AccessRoleServiceInterface;

/**
 * Class AccessRoleReadService
 *
 * @package Gambio\Admin\Modules\AccessRole\App
 */
class AccessRoleReadService implements AccessRoleServiceInterface
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
    public function getAccessRoles(): AccessRoles
    {
        return $this->repository->getAccessRoles();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAccessRolesByAdmin(int $adminId): AccessRoles
    {
        return $this->repository->getAccessRolesByAdmin($this->factory->createAdminId($adminId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAccessRoleById(int $roleId): AccessRole
    {
        return $this->repository->getAccessRoleById($this->factory->createAccessRoleId($roleId));
    }
}