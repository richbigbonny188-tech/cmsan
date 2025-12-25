<?php
/* --------------------------------------------------------------
   AdminRepository.php 2021-04-07
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
use Gambio\Admin\Modules\Admin\Services\Exceptions\AdminDoesNotExistException;
use Gambio\Admin\Modules\Admin\Services\Exceptions\StorageOfAdminsFailedException;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AdminRepository
 *
 * @package Gambio\Admin\Modules\Admin\App\Data
 */
class AdminRepository extends AbstractEventDispatchingRepository
{
    /**
     * @var AdminMapper
     */
    private $mapper;
    
    /**
     * @var AdminReader
     */
    private $reader;
    
    /**
     * @var AdminWriter
     */
    private $writer;
    
    
    /**
     * AdminRepository constructor.
     *
     * @param AdminMapper              $mapper
     * @param AdminReader              $reader
     * @param AdminWriter              $writer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AdminMapper $mapper,
        AdminReader $reader,
        AdminWriter $writer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
        
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @return Admins
     */
    public function getAdmins(): Admins
    {
        return $this->mapper->mapAdmins($this->reader->getAdminsData());
    }
    
    
    /**
     * @param AdminId $adminId
     *
     * @return Admin
     *
     * @throws AdminDoesNotExistException
     */
    public function getAdminById(AdminId $adminId): Admin
    {
        return $this->mapper->mapAdmin($this->reader->getAdminDataById($adminId));
    }
    
    
    /**
     * @param Admin ...$admins
     *
     * @return AdminIds
     *
     * @throws StorageOfAdminsFailedException
     */
    public function storeAdmins(Admin ...$admins): AdminIds
    {
        $ids = $this->writer->storeAdmins(...$admins);
        foreach ($admins as $admin) {
            $this->dispatchEntityEvents($admin);
        }
        
        return $this->mapper->mapAdminIds($ids);
    }
}