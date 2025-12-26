<?php
/*--------------------------------------------------------------------
 ProductOptionStock.php 2021-11-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model\ValueObjects;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;

/**
 * Class ProductOptionStock
 *
 * @package    Gambio\Admin\Modules\ProductOption\Model\ValueObjects
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock
 */
class ProductOptionStock extends AdditionalOptionStock
{
}