<?php
/*--------------------------------------------------------------------
 ProductOptionCombinationAlreadyExistsException.php 2023-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model\Exceptions;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionAlreadyExistsException;

/**
 * Class ProductOptionCombinationAlreadyExistsException
 *
 * @package    Gambio\Admin\Modules\ProductOption\Model\Exceptions
  * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionAlreadyExistsException
 */
class ProductOptionAlreadyExistsException extends AdditionalOptionAlreadyExistsException
{
}