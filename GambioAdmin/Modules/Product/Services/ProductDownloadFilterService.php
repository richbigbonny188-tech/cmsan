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

namespace Gambio\Admin\Modules\Product\Services;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;

/**
 * Interface ProductDownloadFilterService
 *
 * @package Gambio\Admin\Modules\Product\Services\Proxies
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