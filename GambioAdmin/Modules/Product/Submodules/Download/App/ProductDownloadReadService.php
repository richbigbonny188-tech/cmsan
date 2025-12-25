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

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFactory;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository as ProductDownloadRepositoryInterface;

/**
 * Class ProductDownloadReadService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App
 */
class ProductDownloadReadService implements ProductDownloadReadServiceInterface
{
    /**
     * ProductDownloadReadService constructor.
     *
     * @param ProductDownloadRepositoryInterface $repository
     * @param ProductDownloadFactory             $factory
     */
    public function __construct(
        private ProductDownloadRepositoryInterface $repository,
        private ProductDownloadFactory $factory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsByProductId(int $productId): ProductDownloads
    {
        return $this->repository->getProductDownloadsByProductId($this->factory->createProductId($productId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadById(int $productOptionId): ProductDownload
    {
        return $this->repository->getProductDownloadById($this->factory->createProductOptionId($productOptionId));
    }
}