<?php
/* --------------------------------------------------------------
   AccessGroupFactory.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Services;

use Gambio\Admin\Modules\AccessGroup\Model\AccessGroup;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroupIds;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroupItems;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroups;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupDescriptions;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItemType;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupNames;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\ParentAccessGroupId;

/**
 * Class AccessGroupFactory
 *
 * @package Gambio\Admin\Modules\AccessGroup\Services
 */
class AccessGroupFactory
{
    /**
     * Creates and returns an access group.
     *
     * @param int              $id
     * @param int|null         $parentGroupId
     * @param array            $names
     * @param array            $descriptions
     * @param AccessGroupItems $items
     * @param int              $sortOrder
     * @param bool             $isProtected
     *
     * @return AccessGroup
     */
    public function createAccessGroup(
        int $id,
        ?int $parentGroupId,
        array $names,
        array $descriptions,
        AccessGroupItems $items,
        int $sortOrder,
        bool $isProtected
    ): AccessGroup {
        if ($parentGroupId === null) {
            return AccessGroup::createWithoutParent($this->createAccessGroupId($id),
                                                    $this->createAccessGroupNames($names),
                                                    $this->createAccessGroupDescriptions($descriptions),
                                                    $items,
                                                    $sortOrder,
                                                    $isProtected);
        }
        
        return AccessGroup::createWithParent($this->createAccessGroupId($id),
                                             $this->createParentAccessGroupId($parentGroupId),
                                             $this->createAccessGroupNames($names),
                                             $this->createAccessGroupDescriptions($descriptions),
                                             $items,
                                             $sortOrder,
                                             $isProtected);
    }
    
    
    /**
     * Creates and returns a collection of access groups.
     *
     * @param AccessGroup ...$groups
     *
     * @return AccessGroups
     */
    public function createAccessGroups(AccessGroup ...$groups): AccessGroups
    {
        return AccessGroups::create(...$groups);
    }
    
    
    /**
     * Creates and returns an access group ID.
     *
     * @param int $id
     *
     * @return AccessGroupId
     */
    public function createAccessGroupId(int $id): AccessGroupId
    {
        return AccessGroupId::create($id);
    }
    
    
    /**
     * Creates and returns an access group parent ID.
     *
     * @param int $id
     *
     * @return ParentAccessGroupId
     */
    public function createParentAccessGroupId(int $id): ParentAccessGroupId
    {
        return ParentAccessGroupId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of access group IDs.
     *
     * @param AccessGroupId ...$ids
     *
     * @return AccessGroupIds
     */
    public function createAccessGroupIds(AccessGroupId ...$ids): AccessGroupIds
    {
        return AccessGroupIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns an access group item.
     *
     * @param string $type
     * @param string $descriptor
     *
     * @return AccessGroupItem
     */
    public function createAccessGroupItem(string $type, string $descriptor): AccessGroupItem
    {
        return AccessGroupItem::create(AccessGroupItemType::create($type), $descriptor);
    }
    
    
    /**
     * Creates and returns a collection of access group items.
     *
     * @param AccessGroupItem ...$groupItems
     *
     * @return AccessGroupItems
     */
    public function createAccessGroupItems(AccessGroupItem ...$groupItems): AccessGroupItems
    {
        return AccessGroupItems::create(...$groupItems);
    }
    
    
    /**
     * Creates and returns a collection of access group names.
     *
     * @param array $names
     *
     * @return AccessGroupNames
     */
    public function createAccessGroupNames(array $names): AccessGroupNames
    {
        return AccessGroupNames::create($names);
    }
    
    
    /**
     * Creates and returns a collection of access group descriptions.
     *
     * @param array $descriptions
     *
     * @return AccessGroupDescriptions
     */
    public function createAccessGroupDescriptions(array $descriptions): AccessGroupDescriptions
    {
        return AccessGroupDescriptions::create($descriptions);
    }
}