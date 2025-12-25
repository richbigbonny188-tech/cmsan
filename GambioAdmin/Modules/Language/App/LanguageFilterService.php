<?php
/* --------------------------------------------------------------
   LanguageFilterService.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App;

use Gambio\Admin\Modules\Language\App\Data\Filter\LanguageFilterFactory;
use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Services\LanguageFilterService as LanguageFilterServiceInterface;
use Gambio\Admin\Modules\Language\Services\LanguageRepository;

/**
 * Class LanguageFilterService
 *
 * @package Gambio\Admin\Modules\Language\App
 */
class LanguageFilterService implements LanguageFilterServiceInterface
{
    /**
     * @var LanguageRepository
     */
    private $repository;
    
    /**
     * @var LanguageFilterFactory
     */
    private $factory;
    
    
    /**
     * LanguageFilterService constructor.
     *
     * @param LanguageRepository    $repository
     * @param LanguageFilterFactory $factory
     */
    public function __construct(LanguageRepository $repository, LanguageFilterFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterLanguages(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): Languages {
        return $this->repository->filterLanguages($this->factory->createFilters($filters),
                                                  $this->factory->createSorting($sorting),
                                                  $this->factory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguagesTotalCount(array $filters): int
    {
        return $this->repository->getLanguagesTotalCount($this->factory->createFilters($filters));
    }
}