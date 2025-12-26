<?php
/* --------------------------------------------------------------
   RoleFromAdminRemoved.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\Model\Events;

use Gambio\Admin\Modules\Admin\Model\ValueObjects\AdminId;
use Gambio\Admin\Modules\Admin\Model\ValueObjects\RoleId;

/**
 * Class RoleFromAdminRemoved
 *
 * @package Gambio\Admin\Modules\Admin\Model\Events
 * @codeCoverageIgnore
 */
class RoleFromAdminRemoved
{
    /**
     * @var AdminId
     */
    private $adminId;
    
    /**
     * @var RoleId
     */
    private $roleId;
    
    
    /**
     * RoleFromAdminRemoved constructor.
     *
     * @param AdminId $adminId
     * @param RoleId  $roleId
     */
    private function __construct(AdminId $adminId, RoleId $roleId)
    {
        $this->adminId = $adminId;
        $this->roleId  = $roleId;
    }
    
    
    /**
     * @param AdminId $adminId
     * @param RoleId  $roleId
     *
     * @return RoleFromAdminRemoved
     */
    public static function create(AdminId $adminId, RoleId $roleId): RoleFromAdminRemoved
    {
        return new self($adminId, $roleId);
    }
    
    
    /**
     * @return AdminId
     */
    public function adminId(): AdminId
    {
        return $this->adminId;
    }
    
    
    /**
     * @return RoleId
     */
    public function roleId(): RoleId
    {
        return $this->roleId;
    }
}