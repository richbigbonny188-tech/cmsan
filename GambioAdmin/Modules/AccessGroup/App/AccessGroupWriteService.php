<?php
/* --------------------------------------------------------------
   AccessGroupWriteService.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\App;

use Gambio\Admin\Modules\AccessGroup\App\Data\AccessGroupRepository;
use Gambio\Admin\Modules\AccessGroup\Model\AccessGroup;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroupIds;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupFactory;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupWriteService as AccessGroupWriteServiceInterface;

/**
 * Class AccessGroupWriteService
 *
 * @package Gambio\Admin\Modules\AccessGroup\App
 */
class AccessGroupWriteService implements AccessGroupWriteServiceInterface
{
    /**
     * @var AccessGroupRepository
     */
    private $repository;
    
    /**
     * @var AccessGroupFactory
     */
    private $factory;
    
    
    /**
     * GroupService constructor.
     *
     * @param AccessGroupRepository $repository
     * @param AccessGroupFactory    $factory
     */
    public function __construct(AccessGroupRepository $repository, AccessGroupFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createAccessGroup(
        array $names,
        array $descriptions,
        int $sortOrder,
        bool $isProtected = false,
        ?int $parentGroupId = null
    ): AccessGroup {
        $id = $this->repository->createAccessGroup($this->factory->createAccessGroupNames($names),
                                                   $this->factory->createAccessGroupDescriptions($descriptions),
                                                   $sortOrder,
                                                   $isProtected,
                                                   $parentGroupId);
        
        return $this->factory->createAccessGroup($id->value(),
                                                 $parentGroupId,
                                                 $names,
                                                 $descriptions,
                                                 $this->factory->createAccessGroupItems(),
                                                 $sortOrder,
                                                 $isProtected);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAccessGroups(AccessGroup ...$groups): AccessGroupIds
    {
        return $this->repository->storeAccessGroups(...$groups);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAccessGroups(int ...$groupIds): void
    {
        $ids = array_map([$this->factory, 'createAccessGroupId'], $groupIds);
        
        $this->repository->deleteAccessGroups($this->factory->createAccessGroupIds(...$ids));
    }
}