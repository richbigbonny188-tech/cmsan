<?php
/*--------------------------------------------------------------
   ProductDownloadReadServiceProxy.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Services as Submodule;

/**
 * Class ProductDownloadReadServiceProxy
 *
 * @package Gambio\Admin\Modules\Product\App\Proxies
 */
class ProductDownloadReadService implements ProductDownloadReadServiceInterface
{
    public function __construct(private Submodule\ProductDownloadReadService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsByProductId(int $productId): ProductDownloads
    {
        return $this->service->getProductDownloadsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadById(int $additionalOptionId): ProductDownload
    {
        return $this->service->getProductDownloadById($additionalOptionId);
    }
}