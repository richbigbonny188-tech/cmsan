<?php
/* --------------------------------------------------------------
   ProductMainImageInUseServiceInterface.php 2023-11-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

/**
 * Interface ProductMainImageInUseServiceInterface
 *
 * @package Gambio\MainComponents\Services\Core\ProductImageInUse
 */
interface ProductMainImageInUseServiceInterface
{
    /**
     * @param int|null $exceptByProductId
     *
     * @return ProductImageNameDTO[]
     */
    public function mainImagesInUse(?int $exceptByProductId = null): array;
    
    
    /**
     * @param string   $filePath
     * @param int|null $exceptByProductId
     *
     * @return bool
     */
    public function mainImageIsInUse(string $filePath, ?int $exceptByProductId = null): bool;
}