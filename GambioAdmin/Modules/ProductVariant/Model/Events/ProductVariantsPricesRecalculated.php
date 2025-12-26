<?php
/*--------------------------------------------------------------
   ProductVariantsPricesRecalculated.php 2022-02-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\Model\Events;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariantIds;

/**
 * Class ProductVariantsPricesRecalculated
 *
 * @package Gambio\Admin\Modules\ProductVariant\Model\Events
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\ProductVariantsPricesRecalculated
 * @codeCoverageIgnore
 */
class ProductVariantsPricesRecalculated
    extends \Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\ProductVariantsPricesRecalculated
{
}