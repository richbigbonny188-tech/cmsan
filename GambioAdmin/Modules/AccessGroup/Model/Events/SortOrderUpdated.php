<?php
/* --------------------------------------------------------------
   SortOrderUpdated.php 2020-10-21
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

/**
 * Class SortOrderUpdated
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Events
 * @codeCoverageIgnore
 */
class SortOrderUpdated
{
    /**
     * @var AccessGroupId
     */
    private $groupId;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    
    /**
     * SortOrderUpdated constructor.
     *
     * @param AccessGroupId $groupId
     * @param int           $sortOrder
     */
    private function __construct(AccessGroupId $groupId, int $sortOrder)
    {
        $this->groupId   = $groupId;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @param AccessGroupId $groupId
     * @param int           $sortOrder
     *
     * @return SortOrderUpdated
     */
    public static function create(AccessGroupId $groupId, int $sortOrder): SortOrderUpdated
    {
        return new self($groupId, $sortOrder);
    }
    
    
    /**
     * @return AccessGroupId
     */
    public function accessGroupId(): AccessGroupId
    {
        return $this->groupId;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}