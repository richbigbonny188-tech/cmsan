<?php
/* --------------------------------------------------------------
   AdminWriteService.php 2020-10-21
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
use Gambio\Admin\Modules\Admin\Model\Collections\AdminIds;
use Gambio\Admin\Modules\Admin\Services\AdminWriteService as AdminWriteServiceInterface;

/**
 * Class AdminWriteService
 *
 * @package Gambio\Admin\Modules\Admin\App
 */
class AdminWriteService implements AdminWriteServiceInterface
{
    /**
     * @var AdminRepository
     */
    private $repository;
    
    
    /**
     * AdminWriteService constructor.
     *
     * @param AdminRepository $repository
     */
    public function __construct(AdminRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdmins(Admin ...$admins): AdminIds
    {
        return $this->repository->storeAdmins(...$admins);
    }
}