<?php
/* --------------------------------------------------------------
   ProductConditionNoticeUsedProductService.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\Services;

/**
 * Interface ProductConditionNoticeUsedProductService
 *
 * @package GXModules\Gambio\ProductConditionNotice\Services
 */
interface ProductConditionNoticeUsedProductService
{
    /**
     * Returns true if one of the given product ID belongs to a product that is marked as used or renewed.
     *
     * @param int ...$productIds
     *
     * @return bool
     */
    public function containsAUsedOrRenewedProduct(int ...$productIds): bool;
}