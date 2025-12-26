<?php
/* --------------------------------------------------------------
   SortOrderUpdated.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\Events;

use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;

/**
 * Class SortOrderUpdated
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Events
 * @codeCoverageIgnore
 */
class SortOrderUpdated
{
    /**
     * @var AccessRoleId
     */
    private $roleId;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    
    /**
     * SortOrderUpdated constructor.
     *
     * @param AccessRoleId $roleId
     * @param int          $sortOrder
     */
    private function __construct(AccessRoleId $roleId, int $sortOrder)
    {
        $this->roleId    = $roleId;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @param AccessRoleId $roleId
     * @param int          $sortOrder
     *
     * @return SortOrderUpdated
     */
    public static function create(AccessRoleId $roleId, int $sortOrder): SortOrderUpdated
    {
        return new self($roleId, $sortOrder);
    }
    
    
    /**
     * @return AccessRoleId
     */
    public function accessRoleId(): AccessRoleId
    {
        return $this->roleId;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}