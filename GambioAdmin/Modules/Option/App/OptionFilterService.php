<?php
/* --------------------------------------------------------------
   OptionFilterService.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App;

use Gambio\Admin\Modules\Option\App\Data\Filter\OptionFilterFactory;
use Gambio\Admin\Modules\Option\Model\Collections\Options;
use Gambio\Admin\Modules\Option\Services\OptionFilterService as OptionFilterServiceInterface;
use Gambio\Admin\Modules\Option\Services\OptionRepository as OptionRepositoryInterface;

/**
 * Class OptionFilterService
 *
 * @package Gambio\Admin\Modules\Option\App
 */
class OptionFilterService implements OptionFilterServiceInterface
{
    /**
     * @var OptionRepositoryInterface
     */
    private $repository;
    
    /**
     * @var OptionFilterFactory
     */
    private $filterFactory;
    
    
    /**
     * OptionFilterService constructor.
     *
     * @param OptionRepositoryInterface $repository
     * @param OptionFilterFactory       $filterFactory
     */
    public function __construct(OptionRepositoryInterface $repository, OptionFilterFactory $filterFactory)
    {
        $this->repository    = $repository;
        $this->filterFactory = $filterFactory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterOptions(array $filters, ?string $sorting = null, int $limit = 25, int $offset = 0): Options
    {
        return $this->repository->filterOptions($this->filterFactory->createFilters($filters),
                                                $this->filterFactory->createSorting($sorting),
                                                $this->filterFactory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getOptionsTotalCount(array $filters): int
    {
        return $this->repository->getOptionsTotalCount($this->filterFactory->createFilters($filters));
    }
}