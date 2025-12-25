<?php
/*--------------------------------------------------------------------
 ProductOptionId.php 2023-06-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\Model\ValueObjects;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductOptionId
 *
 * @package    Gambio\Admin\Modules\ProductDownload\Model\ValueObjects
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId
 */
class ProductOptionId extends AdditionalOptionId
{

}