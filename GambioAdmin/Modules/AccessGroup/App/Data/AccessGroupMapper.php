<?php
/* --------------------------------------------------------------
   AccessGroupMapper.php 2020-10-21
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
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupFactory;

/**
 * Class AccessGroupMapper
 *
 * @package Gambio\Admin\Modules\AccessGroup\App\Data
 */
class AccessGroupMapper
{
    /**
     * @var AccessGroupFactory
     */
    private $factory;
    
    
    /**
     * AccessGroupMapper constructor.
     *
     * @param AccessGroupFactory $factory
     */
    public function __construct(AccessGroupFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $itemData
     *
     * @return AccessGroupItem
     */
    private function mapAccessGroupItem(array $itemData): AccessGroupItem
    {
        return $this->factory->createAccessGroupItem($itemData['type'], $itemData['descriptor']);
    }
    
    
    /**
     * @param array $groupData
     *
     * @return AccessGroup
     */
    public function mapAccessGroup(array $groupData): AccessGroup
    {
        $groupItems = array_map([$this, 'mapAccessGroupItem'], $groupData['items']);
        
        return $this->factory->createAccessGroup($groupData['id'],
                                                 $groupData['parentGroupId'],
                                                 $groupData['names'],
                                                 $groupData['descriptions'],
                                                 $this->factory->createAccessGroupItems(...$groupItems),
                                                 $groupData['sortOrder'],
                                                 $groupData['isProtected']);
    }
    
    
    /**
     * @param array $groupsData
     *
     * @return AccessGroups
     */
    public function mapAccessGroups(array $groupsData): AccessGroups
    {
        $groups = array_map([$this, 'mapAccessGroup'], $groupsData);
        
        return $this->factory->createAccessGroups(...$groups);
    }
    
    
    /**
     * @param int $groupId
     *
     * @return AccessGroupId
     */
    public function mapAccessGroupId(int $groupId): AccessGroupId
    {
        return $this->factory->createAccessGroupId($groupId);
    }
    
    
    /**
     * @param array $groupIds
     *
     * @return AccessGroupIds
     */
    public function mapAccessGroupIds(array $groupIds): AccessGroupIds
    {
        $groupIds = array_map([$this, 'mapAccessGroupId'], $groupIds);
        
        return $this->factory->createAccessGroupIds(...$groupIds);
    }
}