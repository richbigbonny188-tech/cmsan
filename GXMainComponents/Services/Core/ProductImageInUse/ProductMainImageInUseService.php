<?php
/* --------------------------------------------------------------
   ProductMainImageInUseService.php 2023-11-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

/**
 * Class ProductMainImageInUseService
 *
 * @package Gambio\MainComponents\Services\Core\ProductImageInUse
 */
class ProductMainImageInUseService implements ProductMainImageInUseServiceInterface
{
    
    /**
     * @param ProductMainImageRepositoryInterface $productMainImageRepository
     */
    public function __construct(protected ProductMainImageRepositoryInterface $productMainImageRepository)
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainImagesInUse(?int $exceptByProductId = null): array
    {
        return $this->productMainImageRepository->mainImagesInUse($exceptByProductId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainImageIsInUse(string $filePath, ?int $exceptByProductId = null): bool
    {
        return $this->productMainImageRepository->mainImageIsInUse($filePath, $exceptByProductId);
    }
}