<?php
/*--------------------------------------------------------------------
 ProductDownloadsSortOrderUpdated.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductDownloadsSortOrderUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Events
 */
class ProductDownloadsSortOrderUpdated
{
    /**
     * ProductDownloadsSortOrderUpdated constructor.
     *
     * @param AdditionalOptionId $productOptionId
     * @param int             $sortOrder
     */
    private function __construct(private AdditionalOptionId $productOptionId, private int $sortOrder) { }
    
    
    /**
     * @param AdditionalOptionId $productOptionId
     * @param int             $sortOrder
     *
     * @return ProductDownloadsSortOrderUpdated
     */
    public static function create(AdditionalOptionId $productOptionId, int $sortOrder): ProductDownloadsSortOrderUpdated
    {
        return new static($productOptionId, $sortOrder);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function productOptionId(): AdditionalOptionId
    {
        return $this->productOptionId;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}