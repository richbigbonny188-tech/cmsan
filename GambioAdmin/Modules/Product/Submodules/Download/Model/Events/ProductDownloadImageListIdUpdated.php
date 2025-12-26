<?php
/*--------------------------------------------------------------------
 ProductDownloadImageListIdUpdated.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductDownloadImageListIdUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Events
 */
class ProductDownloadImageListIdUpdated
{
    /**
     * ProductDownloadImageListIdUpdated constructor.
     *
     * @param AdditionalOptionId $productOptionId
     * @param ImageListId     $imageListId
     */
    private function __construct(
        private AdditionalOptionId $productOptionId,
        private ImageListId        $imageListId
    ) {
    }
    
    
    /**
     * @param AdditionalOptionId $productOptionId
     * @param ImageListId     $imageListId
     *
     * @return ProductDownloadImageListIdUpdated
     */
    public static function create(
        AdditionalOptionId $productOptionId,
        ImageListId        $imageListId
    ): ProductDownloadImageListIdUpdated {
        return new static($productOptionId, $imageListId);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function productOptionId(): AdditionalOptionId
    {
        return $this->productOptionId;
    }
    
    
    /**
     * @return ImageListId
     */
    public function imageListId(): ImageListId
    {
        return $this->imageListId;
    }
}