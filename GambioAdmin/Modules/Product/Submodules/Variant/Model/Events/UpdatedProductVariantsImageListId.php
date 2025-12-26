<?php
/*--------------------------------------------------------------
   UpdatedProductVariantsImageListId.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class UpdatedProductVariantsImageListId
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class UpdatedProductVariantsImageListId
{
    /**
     * UpdatedProductVariantsImageListId constructor.
     *
     * @param ProductVariantId $variantId
     * @param ImageListId      $imageListId
     */
    private function __construct(private ProductVariantId $variantId, private ImageListId $imageListId)
    {
    }
    
    
    /**
     * @param ProductVariantId $variantId
     * @param ImageListId      $imageListId
     *
     * @return UpdatedProductVariantsImageListId
     */
    public static function create(
        ProductVariantId $variantId,
        ImageListId      $imageListId
    ): UpdatedProductVariantsImageListId {
        return new static($variantId, $imageListId);
    }
    
    
    /**
     * @return ImageListId
     */
    public function imageListId(): ImageListId
    {
        return $this->imageListId;
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->variantId;
    }
}