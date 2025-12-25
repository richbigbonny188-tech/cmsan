<?php
/* --------------------------------------------------------------
  UpdateServiceRepositoryInterface.php 2020-01-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Interfaces;

use Gambio\ProductImageList\Image\Collections\TextCollection;

/**
 * Interface UpdateServiceRepositoryInterface
 * @package Gambio\ProductImageList\UpdateService\Interfaces
 */
interface UpdateServiceRepositoryInterface
{
    /**
     * @param UpdateMultipleSortingDtoInterface $sortOrders
     */
    public function updateImagesSort(UpdateMultipleSortingDtoInterface $sortOrders) : void;
    
    
    /**
     * @param TextCollection $text
     */
    public function updateImageText(TextCollection $text) : void;
    
    
    /**
     * @param UpdateImageListNameDtoInterface $dto
     */
    public function updateImageListName(UpdateImageListNameDtoInterface $dto): void;
    
    
    /**
     * @param UpdateImageListCombinationAssignmentDtoInterface $dto
     */
    public function updateImageListCombiAssignment(UpdateImageListCombinationAssignmentDtoInterface $dto): void;
    
    
    /**
     * @param UpdateImageListAttributeAssignmentDtoInterface $dto
     */
    public function updateImageListAttributeAssigment(UpdateImageListAttributeAssignmentDtoInterface $dto): void;
}