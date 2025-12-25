<?php
/* --------------------------------------------------------------
   ProductImageFactory.php 2023-11-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);


/**
 * Class ProductImageFactory
 *
 * @package Gambio\MainComponents\Services\Core\ProductImageInUse\Repository\Factories
 */
class ProductImageFactory
{
    
    /**
     * Creates a ProductImageNameDTO object
     *
     * @param string $filePath
     *
     * @return ProductImageNameDTO
     */
    public function createImageNameDTO(string $filePath): ProductImageNameDTO
    {
        return new ProductImageNameDTO($filePath);
    }
}