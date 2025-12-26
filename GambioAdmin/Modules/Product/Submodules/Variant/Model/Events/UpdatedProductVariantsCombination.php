<?php
/*--------------------------------------------------------------
   UpdatedProductVariantsCombination.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class UpdatedProductVariantsCombination
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class UpdatedProductVariantsCombination
{
    /**
     * UpdatedProductVariantsCombination constructor.
     *
     * @param ProductVariantId        $variantId
     * @param OptionAndOptionValueIds $combinations
     */
    private function __construct(protected ProductVariantId $variantId, protected OptionAndOptionValueIds $combinations)
    {
    }
    
    
    /**
     * @param ProductVariantId        $variantId
     * @param OptionAndOptionValueIds $combinations
     *
     * @return UpdatedProductVariantsCombination
     */
    public static function create(
        ProductVariantId        $variantId,
        OptionAndOptionValueIds $combinations
    ): UpdatedProductVariantsCombination {
        return new static($variantId, $combinations);
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->variantId;
    }
    
    
    /**
     * @return OptionAndOptionValueIds
     */
    public function combinations(): OptionAndOptionValueIds
    {
        return $this->combinations;
    }
}