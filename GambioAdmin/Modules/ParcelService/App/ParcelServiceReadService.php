<?php
/* --------------------------------------------------------------
   ParcelServiceReadService.php 2020-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServices;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFactory;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceReadService as ParcelServiceReadServiceInterface;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceRepository;

/**
 * Class ParcelServiceService
 *
 * @package Gambio\Admin\Modules\ParcelService
 */
class ParcelServiceReadService implements ParcelServiceReadServiceInterface
{
    /**
     * @var ParcelServiceRepository
     */
    private $repository;
    
    /**
     * @var ParcelServiceFactory
     */
    private $factory;
    
    
    /**
     * ParcelServiceReadService constructor.
     *
     * @param ParcelServiceRepository $repository
     * @param ParcelServiceFactory    $factory
     */
    public function __construct(ParcelServiceRepository $repository, ParcelServiceFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getParcelServices(): ParcelServices
    {
        return $this->repository->getAllParcelServices();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getParcelServiceById(int $id): ParcelService
    {
        return $this->repository->getParcelServiceById($this->factory->createParcelServiceId($id));
    }
}