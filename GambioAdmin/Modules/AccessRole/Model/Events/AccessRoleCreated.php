<?php
/* --------------------------------------------------------------
   AccessRoleCreated.php 2020-10-20
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
 * Class AccessRoleCreated
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Events
 * @codeCoverageIgnore
 */
class AccessRoleCreated
{
    /**
     * @var AccessRoleId
     */
    private $roleId;
    
    
    /**
     * AccessRoleCreated constructor.
     *
     * @param AccessRoleId $roleId
     */
    private function __construct(AccessRoleId $roleId)
    {
        $this->roleId = $roleId;
    }
    
    
    /**
     * @param AccessRoleId $roleId
     *
     * @return AccessRoleCreated
     */
    public static function create(AccessRoleId $roleId): AccessRoleCreated
    {
        return new self($roleId);
    }
    
    
    /**
     * @return AccessRoleId
     */
    public function accessRoleId(): AccessRoleId
    {
        return $this->roleId;
    }
}