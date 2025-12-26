<?php
/*--------------------------------------------------------------------
 ProductDownloadReadService.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Services;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadDoesNotExistException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;

/**
 * Interface ProductDownloadReadService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Services
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