<?php
/*--------------------------------------------------------------
   ProductDownloadFilterServiceProxy.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\ProductDownloadFilterService as ProductDownloadFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Services as Submodule;

/**
 * Class ProductDownloadFilterServiceProxy
 *
 * @package Gambio\Admin\Modules\Product\App\Proxies
 */
class ProductDownloadFilterService implements ProductDownloadFilterServiceInterface
{
    public function __construct(private Submodule\ProductDownloadFilterService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function filterProductDownloads(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): ProductDownloads {
        return $this->service->filterProductDownloads($productId, $filters, $sorting, $limit, $offset);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsTotalCount(int $productId, array $filters): int
    {
        return $this->service->getProductDownloadsTotalCount($productId, $filters);
    }
}