<?php
/* --------------------------------------------------------------
   ProductMainImageRepository.php 2023-11-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);


/**
 * Class ProductMainImageRepository
 *
 * @package Gambio\MainComponents\Services\Core\ProductImageInUse\Repository
 */
class ProductMainImageRepository implements ProductMainImageRepositoryInterface
{
    
    /**
     * @param ProductMainImageReaderInterface $productMainImageReader
     */
    public function __construct(protected ProductMainImageReaderInterface $productMainImageReader)
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainImagesInUse(?int $exceptByProductId = null): array
    {
        return $this->productMainImageReader->mainImagesInUse($exceptByProductId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainImageIsInUse(string $filePath, ?int $exceptByProductId = null): bool
    {
        return $this->productMainImageReader->mainImageIsInUse($filePath, $exceptByProductId);
    }
}