<?php
/*--------------------------------------------------------------
   RecalculateProductVariantPriceService.php 2023-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant as Submodule;
use Gambio\Admin\Modules\ProductVariant\Services\RecalculateProductVariantPriceService as RecalculateProductVariantPriceServiceInterface;

/**
 * Class RecalculateProductVariantPriceService
 *
 * @package Gambio\Admin\Modules\ProductVariant\App
 */
class RecalculateProductVariantPriceService implements RecalculateProductVariantPriceServiceInterface
{
    public function __construct(private Submodule\Services\RecalculateProductVariantPriceService $service){}

    /**
     * @inheritDoc
     */
    public function recalculateForVariantsWithOptionValue(int $optionId, int $optionValueId): void
    {
        $this->service->recalculateForVariantsWithOptionValue($optionId, $optionValueId);
    }
}