<?php
/* --------------------------------------------------------------
   RecalculateProductVariantsPrices.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Services;

/**
 * Interface RecalculateProductVariantPriceService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Services
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