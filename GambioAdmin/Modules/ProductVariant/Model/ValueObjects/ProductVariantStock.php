<?php
/*--------------------------------------------------------------
   ProductVariantStock.php 2021-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\Model\ValueObjects;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * Class ProductVariantStock
 *
 * @package Gambio\Admin\Modules\ProductVariant\Model\ValueObjects
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock
 */
class ProductVariantStock
    extends \Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock
{

}
