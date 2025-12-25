<?php
/* --------------------------------------------------------------
   TrackingCodeService.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App;

use Gambio\Admin\Modules\TrackingCode\App\Data\Filter\TrackingCodeFilterFactory;
use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodes;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFilterService as TrackingCodeFilterServiceInterface;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository;

/**
 * Class TrackingCodeService
 *
 * @package Gambio\Admin\Modules\TrackingCode
 */
class TrackingCodeFilterService implements TrackingCodeFilterServiceInterface
{
    /**
     * @var TrackingCodeRepository
     */
    private $repository;
    
    /**
     * @var TrackingCodeFilterFactory
     */
    private $factory;
    
    
    /**
     * TrackingCodeFilterService constructor.
     *
     * @param TrackingCodeRepository    $repository
     * @param TrackingCodeFilterFactory $factory
     */
    public function __construct(TrackingCodeRepository $repository, TrackingCodeFilterFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterTrackingCodes(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): TrackingCodes {
        return $this->repository->filterTrackingCodes($this->factory->createFilters($filters),
                                                      $this->factory->createSorting($sorting),
                                                      $this->factory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getTrackingCodesTotalCount(array $filters): int
    {
        return $this->repository->getTrackingCodesTotalCount($this->factory->createFilters($filters));
    }
}