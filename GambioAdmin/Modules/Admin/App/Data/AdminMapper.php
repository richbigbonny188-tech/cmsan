<?php
/* --------------------------------------------------------------
   AdminMapper.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\App\Data;

use Gambio\Admin\Modules\Admin\Model\Admin;
use Gambio\Admin\Modules\Admin\Model\Collections\AdminIds;
use Gambio\Admin\Modules\Admin\Model\Collections\Admins;
use Gambio\Admin\Modules\Admin\Model\ValueObjects\AdminId;
use Gambio\Admin\Modules\Admin\Services\AdminFactory;

/**
 * Class AdminMapper
 *
 * @package Gambio\Admin\Modules\Admin\App\Data
 */
class AdminMapper
{
    /**
     * @var AdminFactory
     */
    private $factory;
    
    
    /**
     * AdminMapper constructor.
     *
     * @param AdminFactory $factory
     */
    public function __construct(AdminFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $adminData
     *
     * @return Admin
     */
    public function mapAdmin(array $adminData): Admin
    {
        return $this->factory->createAdmin($adminData['id'],
                                           $adminData['firstName'],
                                           $adminData['lastName'],
                                           $adminData['assignedRoles']);
    }
    
    
    /**
     * @param array $adminsData
     *
     * @return Admins
     */
    public function mapAdmins(array $adminsData): Admins
    {
        $admins = array_map([$this, 'mapAdmin'], $adminsData);
        
        return $this->factory->createAdmins(...$admins);
    }
    
    
    /**
     * @param int $adminId
     *
     * @return AdminId
     */
    public function mapAdminId(int $adminId): AdminId
    {
        return $this->factory->createAdminId($adminId);
    }
    
    
    /**
     * @param array $adminIds
     *
     * @return AdminIds
     */
    public function mapAdminIds(array $adminIds): AdminIds
    {
        $adminIds = array_map([$this, 'mapAdminId'], $adminIds);
        
        return $this->factory->createAdminIds(...$adminIds);
    }
}