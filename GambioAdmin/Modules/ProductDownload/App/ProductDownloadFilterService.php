<?php
/* --------------------------------------------------------------
  ProductDownloadFilterService.php 2023-06-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\App;

use Gambio\Admin\Modules\Product\Services\ProductDownloadFilterService;
use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadFilterService as ProductDownloadFilterServiceInterface;

/**
 * Class ProductDownloadFilterService
 *
 * @package    Gambio\Admin\Modules\ProductDownload\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11.
 */
class ProductDownloadFilterService implements ProductDownloadFilterServiceInterface
{
    public function __construct(private ProductDownloadFilterService $proxy) { }
    
    
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
        return $this->proxy->filterProductDownloads($productId,
                                                    $filters,
                                                    $sorting,
                                                    $limit,
                                                    $offset);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsTotalCount(int $productId, array $filters): int
    {
        return $this->proxy->getProductDownloadsTotalCount($productId, $filters);
    }
}