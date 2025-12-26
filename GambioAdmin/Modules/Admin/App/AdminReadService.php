<?php
/* --------------------------------------------------------------
   AdminReadService.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\App;

use Gambio\Admin\Modules\Admin\App\Data\AdminRepository;
use Gambio\Admin\Modules\Admin\Model\Admin;
use Gambio\Admin\Modules\Admin\Model\Collections\Admins;
use Gambio\Admin\Modules\Admin\Services\AdminFactory;
use Gambio\Admin\Modules\Admin\Services\AdminReadService as AdminReadServiceInterface;

/**
 * Class AdminReadService
 *
 * @package Gambio\Admin\Modules\Admin\App
 */
class AdminReadService implements AdminReadServiceInterface
{
    /**
     * @var AdminRepository
     */
    private $repository;
    
    /**
     * @var AdminFactory
     */
    private $factory;
    
    
    /**
     * AdminService constructor.
     *
     * @param AdminRepository $repository
     * @param AdminFactory    $factory
     */
    public function __construct(AdminRepository $repository, AdminFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdmins(): Admins
    {
        return $this->repository->getAdmins();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdminById(int $id): Admin
    {
        return $this->repository->getAdminById($this->factory->createAdminId($id));
    }
}