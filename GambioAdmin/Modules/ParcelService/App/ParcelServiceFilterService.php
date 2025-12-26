<?php
/* --------------------------------------------------------------
   ParcelServiceService.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App;

use Gambio\Admin\Modules\ParcelService\App\Data\Filter\ParcelServiceFilterFactory;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServices;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFilterService as ParcelServiceFilterServiceInterface;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceRepository;

/**
 * Class ParcelServiceService
 *
 * @package Gambio\Admin\Modules\ParcelService
 */
class ParcelServiceFilterService implements ParcelServiceFilterServiceInterface
{
    /**
     * @var ParcelServiceRepository
     */
    private $repository;
    
    /**
     * @var ParcelServiceFilterFactory
     */
    private $factory;
    
    
    /**
     * ParcelServiceFilterService constructor.
     *
     * @param ParcelServiceRepository    $repository
     * @param ParcelServiceFilterFactory $factory
     */
    public function __construct(ParcelServiceRepository $repository, ParcelServiceFilterFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterParcelServices(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): ParcelServices {
        return $this->repository->filterParcelServices($this->factory->createFilters($filters),
                                                       $this->factory->createSorting($sorting),
                                                       $this->factory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getParcelServicesTotalCount(array $filters): int
    {
        return $this->repository->getParcelServicesTotalCount($this->factory->createFilters($filters));
    }
}