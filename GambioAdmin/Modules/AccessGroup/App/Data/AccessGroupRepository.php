<?php
/* --------------------------------------------------------------
   AccessGroupRepository.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\App\Data;

use Gambio\Admin\Modules\AccessGroup\Model\AccessGroup;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroupIds;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroups;
use Gambio\Admin\Modules\AccessGroup\Model\Events\AccessGroupCreated;
use Gambio\Admin\Modules\AccessGroup\Model\Events\AccessGroupDeleted;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupDescriptions;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupNames;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\AccessGroupDoesNotExistException;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\DeletionOfAccessGroupsFailedException;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\StorageOfAccessGroupsFailedException;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AccessGroupRepository
 *
 * @package Gambio\Admin\Modules\AccessGroup\App\Data
 */
class AccessGroupRepository extends AbstractEventDispatchingRepository
{
    /**
     * @var AccessGroupMapper
     */
    private $mapper;
    
    /**
     * @var AccessGroupReader
     */
    private $reader;
    
    /**
     * @var AccessGroupWriter
     */
    private $writer;
    
    
    /**
     * AccessGroupRepository constructor.
     *
     * @param AccessGroupMapper        $mapper
     * @param AccessGroupReader        $reader
     * @param AccessGroupWriter        $writer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AccessGroupMapper $mapper,
        AccessGroupReader $reader,
        AccessGroupWriter $writer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
        
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @return AccessGroups
     */
    public function getAccessGroups(): AccessGroups
    {
        return $this->mapper->mapAccessGroups($this->reader->getAccessGroupsData());
    }
    
    
    /**
     * @param AccessGroupId $groupId
     *
     * @return AccessGroup
     *
     * @throws AccessGroupDoesNotExistException
     */
    public function getAccessGroupById(AccessGroupId $groupId): AccessGroup
    {
        return $this->mapper->mapAccessGroup($this->reader->getAccessGroupDataById($groupId));
    }
    
    
    /**
     * @param AccessGroupItem $groupItem
     *
     * @return AccessGroup
     *
     * @throws AccessGroupDoesNotExistException
     */
    public function getAccessGroupByItem(AccessGroupItem $groupItem): AccessGroup
    {
        return $this->mapper->mapAccessGroup($this->reader->getAccessGroupDataByItem($groupItem));
    }
    
    
    /**
     * @param AccessGroupNames        $names
     * @param AccessGroupDescriptions $descriptions
     * @param int                     $sortOrder
     * @param bool                    $isProtected
     * @param int|null                $parentGroupId
     *
     * @return AccessGroupId
     */
    public function createAccessGroup(
        AccessGroupNames $names,
        AccessGroupDescriptions $descriptions,
        int $sortOrder,
        bool $isProtected = false,
        ?int $parentGroupId = null
    ): AccessGroupId {
        $group = $this->writer->createAccessGroup($names, $descriptions, $sortOrder, $isProtected, $parentGroupId);
        $id    = $this->mapper->mapAccessGroupId($group);
        
        $this->dispatchEvent(AccessGroupCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @param AccessGroup ...$groups
     *
     * @return AccessGroupIds
     *
     * @throws StorageOfAccessGroupsFailedException
     */
    public function storeAccessGroups(AccessGroup ...$groups): AccessGroupIds
    {
        $ids = $this->mapper->mapAccessGroupIds($this->writer->storeAccessGroups(...$groups));
        foreach ($groups as $index => $group) {
            $this->dispatchEntityEvents($group);
        }
        
        return $ids;
    }
    
    
    /**
     * @param AccessGroupIds $groupIds
     *
     * @throws DeletionOfAccessGroupsFailedException
     */
    public function deleteAccessGroups(AccessGroupIds $groupIds): void
    {
        $this->writer->deleteAccessGroups($groupIds);
        foreach ($groupIds as $groupId) {
            $this->dispatchEvent(AccessGroupDeleted::create($groupId));
        }
    }
}