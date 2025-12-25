<?php
/*--------------------------------------------------------------------
 ProductDownload.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\Model;

use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadImageListIdUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadsSortOrderUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadsStockUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadsValueCustomizationUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductDownloadStock;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;
use InvalidArgumentException;

/**
 * Class ProductDownload
 *
 * @package Gambio\Admin\Modules\ProductDownload\Model
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload
 */
class ProductDownload extends \Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload
{

}