<?php
/**
 * ProductImageListUpdateServiceInterface.php 2020-1-22
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\ProductImageList\Interfaces;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\ValueObjects\AbstractText;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListAttributeAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListCombinationAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListNameDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateMultipleSortingDtoInterface;

/**
 * Interface ProductImageListUpdateServiceInterface
 * @package Gambio\ProductImageList\Interfaces
 */
interface ProductImageListUpdateServiceInterface
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