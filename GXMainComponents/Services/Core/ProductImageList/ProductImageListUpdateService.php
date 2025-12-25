<?php
/* --------------------------------------------------------------
  ProductImageListUpdateService.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Interfaces\ProductImageListUpdateServiceInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListAttributeAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListCombinationAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListNameDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateMultipleSortingDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateServiceRepositoryInterface;

/**
 * Class ProductImageListUpdateService
 * @package Gambio\ProductImageList
 */
class ProductImageListUpdateService implements ProductImageListUpdateServiceInterface
{
    /**
     * @var UpdateServiceRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ProductImageListUpdateService constructor.
     *
     * @param UpdateServiceRepositoryInterface $repository
     */
    public function __construct(UpdateServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImagesSort(UpdateMultipleSortingDtoInterface $sortOrders): void
    {
        $this->repository->updateImagesSort($sortOrders);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageText(TextCollection $text): void
    {
        $this->repository->updateImageText($text);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListName(UpdateImageListNameDtoInterface $dto): void
    {
        $this->repository->updateImageListName($dto);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListCombiAssignment(UpdateImageListCombinationAssignmentDtoInterface $dto): void
    {
        $this->repository->updateImageListCombiAssignment($dto);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListAttributeAssigment(UpdateImageListAttributeAssignmentDtoInterface $dto): void
    {
        $this->repository->updateImageListAttributeAssigment($dto);
    }
}