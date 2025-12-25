<?php
/*--------------------------------------------------------------------
 ProductDownloadReadService.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\App;

use Gambio\Admin\Modules\Product\Services\ProductDownloadReadService;
use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\ProductDownload\Model\ProductDownload;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;

/**
 * Class ProductDownloadReadService
 *
 * @package Gambio\Admin\Modules\ProductDownload\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11.
 */
class ProductDownloadReadService implements ProductDownloadReadServiceInterface
{
    public function __construct(private ProductDownloadReadService $proxy) { }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsByProductId(int $productId): ProductDownloads
    {
        return $this->proxy->getProductDownloadsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadById(int $productOptionId): ProductDownload
    {
        return $this->proxy->getProductDownloadById($productOptionId);
    }
}