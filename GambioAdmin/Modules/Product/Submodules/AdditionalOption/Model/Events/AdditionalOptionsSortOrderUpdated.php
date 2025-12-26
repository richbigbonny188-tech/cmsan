<?php
/*--------------------------------------------------------------------
 AdditionalOptionsSortOrderUpdated.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;

/**
 * Class AdditionalOptionsSortOrderUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events
 */
class AdditionalOptionsSortOrderUpdated
{
    /**
     * AdditionalOptionsSortOrderUpdated constructor.
     *
     * @param AdditionalOptionId $additionalOptionId
     * @param int                $sortOrder
     */
    private function __construct(
        private AdditionalOptionId $additionalOptionId,
        private int                $sortOrder
    ) {
    }
    
    
    /**
     * @param AdditionalOptionId $productOptionId
     * @param int                $sortOrder
     *
     * @return AdditionalOptionsSortOrderUpdated
     */
    public static function create(
        AdditionalOptionId $productOptionId,
        int                $sortOrder
    ): AdditionalOptionsSortOrderUpdated {
        return new static($productOptionId, $sortOrder);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function additionalOptionId(): AdditionalOptionId
    {
        return $this->additionalOptionId;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}