<?php
/* --------------------------------------------------------------
   ItemFromAccessGroupRemoved.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\Events;

use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;

/**
 * Class ItemFromAccessGroupRemoved
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Events
 * @codeCoverageIgnore
 */
class ItemFromAccessGroupRemoved
{
    /**
     * @var AccessGroupId
     */
    private $groupId;
    
    /**
     * @var AccessGroupItem
     */
    private $groupItem;
    
    
    /**
     * ItemFromAccessGroupRemoved constructor.
     *
     * @param AccessGroupId   $groupId
     * @param AccessGroupItem $groupItem
     */
    private function __construct(AccessGroupId $groupId, AccessGroupItem $groupItem)
    {
        $this->groupId   = $groupId;
        $this->groupItem = $groupItem;
    }
    
    
    /**
     * @param AccessGroupId   $groupId
     * @param AccessGroupItem $groupItem
     *
     * @return ItemFromAccessGroupRemoved
     */
    public static function create(AccessGroupId $groupId, AccessGroupItem $groupItem): ItemFromAccessGroupRemoved
    {
        return new self($groupId, $groupItem);
    }
    
    
    /**
     * @return AccessGroupId
     */
    public function accessGroupId(): AccessGroupId
    {
        return $this->groupId;
    }
    
    
    /**
     * @return AccessGroupItem
     */
    public function accessGroupItem(): AccessGroupItem
    {
        return $this->groupItem;
    }
}