<?php
/*--------------------------------------------------------------------
 ProductDownloadAlreadyExistsException.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\Model\Exceptions;

use Exception;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductId;

/**
 * Class ProductDownloadAlreadyExistsException
 *
 * @package Gambio\Admin\Modules\ProductDownload\Model\Exceptions
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadAlreadyExistsException
 */
class ProductDownloadAlreadyExistsException
    extends \Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadAlreadyExistsException
{

}