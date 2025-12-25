<?php
/* --------------------------------------------------------------
  UpdateServiceRepository.php 2020-01-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Repositories;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListAttributeAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListCombinationAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListNameDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateMultipleSortingDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateServiceDatabaseWriterInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateServiceRepositoryInterface;

/**
 * Class UpdateServiceRepository
 * @package Gambio\ProductImageList\UpdateService\Repositories
 */
class UpdateServiceRepository implements UpdateServiceRepositoryInterface
{
    /**
     * @var UpdateServiceDatabaseWriterInterface
     */
    protected $writer;
    
    
    /**
     * UpdateServiceRepository constructor.
     *
     * @param UpdateServiceDatabaseWriterInterface $writer
     */
    public function __construct(UpdateServiceDatabaseWriterInterface $writer)
    {
        $this->writer = $writer;
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImagesSort(UpdateMultipleSortingDtoInterface $sortOrders): void
    {
        $this->writer->updateImagesSort($sortOrders);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageText(TextCollection $text): void
    {
        $this->writer->updateImageText($text);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListName(UpdateImageListNameDtoInterface $dto): void
    {
        $this->writer->updateImageListName($dto);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListCombiAssignment(UpdateImageListCombinationAssignmentDtoInterface $dto): void
    {
        $this->writer->updateImageListCombiAssignment($dto);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListAttributeAssigment(UpdateImageListAttributeAssignmentDtoInterface $dto): void
    {
        $this->writer->updateImageListAttributeAssigment($dto);
    }
}