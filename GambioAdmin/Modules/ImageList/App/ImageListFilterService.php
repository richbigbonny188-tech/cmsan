<?php
/*--------------------------------------------------------------
   ImageListFilterService.php 2021-05-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App;

use Gambio\Admin\Modules\ImageList\App\Data\Filter\ImageListFilterFactory;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageLists;
use Gambio\Admin\Modules\ImageList\Services\ImageListFilterService as ImageListFilterInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository as ImageListRepositoryInterface;

/**
 * Class ImageListFilterService
 * @package Gambio\Admin\Modules\ImageList\App
 */
class ImageListFilterService implements ImageListFilterInterface
{
    /**
     * @var ImageListRepositoryInterface
     */
    private $repository;
    
    /**
     * @var ImageListFilterFactory
     */
    private $factory;
    
    
    /**
     * ImageListReadService constructor.
     *
     * @param ImageListRepositoryInterface $repository
     * @param ImageListFilterFactory       $factory
     */
    public function __construct(
        ImageListRepositoryInterface $repository,
        ImageListFilterFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterImageLists(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): ImageLists {
        
        $filters    = $this->factory->createFilters($filters);
        $sorting    = $this->factory->createSorting($sorting);
        $pagination = $this->factory->createPagination($limit, $offset);
        
        return $this->repository->filterImageLists($filters, $sorting, $pagination);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListsTotalCount(array $filters): int
    {
        return $this->repository->getImageListsTotalCount($this->factory->createFilters($filters));
    }
}