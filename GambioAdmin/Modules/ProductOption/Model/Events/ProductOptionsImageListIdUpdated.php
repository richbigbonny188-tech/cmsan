<?php
/*--------------------------------------------------------------------
 ProductOptionsImageListIdUpdated.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;

/**
 * Class ProductOptionsImageListIdUpdated
 *
 * @package    Gambio\Admin\Modules\ProductOption\Model\Events
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events\AdditionalOptionImageListIdUpdated
 */
class ProductOptionsImageListIdUpdated
{
    /**
     * @var AdditionalOptionId
     */
    private $productOptionId;
    
    /**
     * @var ImageListId
     */
    private $imageListId;
    
    
    /**
     * ProductOptionsImageListIdUpdated constructor.
     *
     * @param AdditionalOptionId $productOptionId
     * @param ImageListId        $imageListId
     */
    private function __construct(
        AdditionalOptionId $productOptionId,
        ImageListId        $imageListId
    ) {
        $this->productOptionId = $productOptionId;
        $this->imageListId     = $imageListId;
    }
    
    
    /**
     * @param AdditionalOptionId $productOptionId
     * @param ImageListId        $imageListId
     *
     * @return ProductOptionsImageListIdUpdated
     */
    public static function create(
        AdditionalOptionId $productOptionId,
        ImageListId        $imageListId
    ): ProductOptionsImageListIdUpdated {
        return new self($productOptionId, $imageListId);
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