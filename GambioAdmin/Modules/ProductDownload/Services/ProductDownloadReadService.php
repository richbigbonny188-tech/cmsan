<?php
/*--------------------------------------------------------------------
 ProductDownloadReadService.php 2021-09-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\Services;

use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\ProductDownloadDoesNotExistException;
use Gambio\Admin\Modules\ProductDownload\Model\ProductDownload;

/**
 * Interface ProductDownloadReadService
 *
 * @package Gambio\Admin\Modules\ProductDownload\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Services\Proxies\ProductDownloadReadServiceProxy
 */
interface ProductDownloadReadService
{
    /**
     * @param int $productId
     *
     * @return ProductDownloads
     */
    public function getProductDownloadsByProductId(int $productId): ProductDownloads;
    
    
    /**
     * @param int $productOptionId
     *
     * @return ProductDownload
     *
     * @throws ProductDownloadDoesNotExistException
     */
    public function getProductDownloadById(int $productOptionId): ProductDownload;
}