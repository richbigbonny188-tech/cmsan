<?php
/*--------------------------------------------------------------------
 AdditionalOptionsStockUpdated.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;

/**
 * Class AdditionalOptionsStockUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events
 */
class AdditionalOptionsStockUpdated
{
    /**
     * AdditionalOptionsStockUpdated constructor.
     *
     * @param AdditionalOptionId    $additionalOptionId
     * @param AdditionalOptionStock $additionalOptionStock
     */
    private function __construct(
        private AdditionalOptionId    $additionalOptionId,
        private AdditionalOptionStock $additionalOptionStock
    ) {
    }
    
    
    /**
     * @param AdditionalOptionId    $additionalOptionId
     * @param AdditionalOptionStock $additionalOptionStock
     *
     * @return AdditionalOptionsStockUpdated
     */
    public static function create(
        AdditionalOptionId    $additionalOptionId,
        AdditionalOptionStock $additionalOptionStock
    ): AdditionalOptionsStockUpdated {
        return new static($additionalOptionId, $additionalOptionStock);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function additionalOptionId(): AdditionalOptionId
    {
        return $this->additionalOptionId;
    }
    
    
    /**
     * @return AdditionalOptionStock
     */
    public function additionalOptionStock(): AdditionalOptionStock
    {
        return $this->additionalOptionStock;
    }
}