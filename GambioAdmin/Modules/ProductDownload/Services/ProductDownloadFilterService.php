<?php
/*--------------------------------------------------------------------
 ProductDownloadFilterService.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\Services;

use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductDownloads;

/**
 * Interface ProductDownloadFilterService
 *
 * @package    Gambio\Admin\Modules\ProductDownload\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *              submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *              \Gambio\Admin\Modules\Product\Services\Proxies\ProductDownloadFilterServiceProxy
 */
interface ProductDownloadFilterService
{
    /**
     * @param int         $productId
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return ProductDownloads
     */
    public function filterProductDownloads(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): ProductDownloads;
    
    
    /**
     * @param int   $productId
     * @param array $filters
     *
     * @return int
     */
    public function getProductDownloadsTotalCount(int $productId, array $filters): int;
}