<?php
/* --------------------------------------------------------------
   AccessGroupDeleted.php 2020-10-21
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
 * Class AccessGroupDeleted
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Events
 * @codeCoverageIgnore
 */
class AccessGroupDeleted
{
    /**
     * @var AccessGroupId
     */
    private $groupId;
    
    
    /**
     * AccessGroupDeleted constructor.
     *
     * @param AccessGroupId $groupId
     */
    private function __construct(AccessGroupId $groupId)
    {
        $this->groupId = $groupId;
    }
    
    
    /**
     * @param AccessGroupId $groupId
     *
     * @return AccessGroupDeleted
     */
    public static function create(AccessGroupId $groupId): AccessGroupDeleted
    {
        return new self($groupId);
    }
    
    
    /**
     * @return AccessGroupId
     */
    public function accessGroupId(): AccessGroupId
    {
        return $this->groupId;
    }
}