<?php
/* --------------------------------------------------------------
   RecalculateProductVariantsPrices.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\Services;

/**
 * Interface RecalculateProductVariantPriceService
 *
 * @package Gambio\Admin\Modules\ProductVariant\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *              submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *              \Gambio\Admin\Modules\Product\Submodules\Variant\Services\RecalculateProductVariantPriceService
 */
interface RecalculateProductVariantPriceService
{
    /**
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return void
     *
     * @throw PriceRecalculationFailed
     */
    public function recalculateForVariantsWithOptionValue(int $optionId, int $optionValueId): void;
}