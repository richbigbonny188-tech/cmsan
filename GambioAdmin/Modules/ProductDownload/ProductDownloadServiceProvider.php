<?php
/*--------------------------------------------------------------------------------------------------
    ProductDownloadServiceProvider.php 2023-06-26
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload;

use Gambio\Admin\Modules\Product\Services\ProductDownloadFilterService;
use Gambio\Admin\Modules\Product\Services\ProductDownloadReadService;
use Gambio\Admin\Modules\Product\Services\ProductDownloadWriteService;
use Gambio\Admin\Modules\ProductDownload\App\ProductDownloadRepository;
use Gambio\Admin\Modules\ProductDownload\App\ProductDownloadRequestParser;
use Gambio\Admin\Modules\ProductDownload\App\ProductDownloadRequestValidator;
use Gambio\Admin\Modules\ProductDownload\App\ProductDownloadsImageListOperationPermitter;
use Gambio\Admin\Modules\ProductDownload\App\ProductDownloadsOptionOperationPermitter;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadFactory;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadFilterService as ProductDownloadFilterServiceInterface;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadRepository as ProductDownloadRepositoryInterface;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadWriteService as ProductDownloadWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11.
 */
class ProductDownloadServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            ProductDownloadFilterServiceInterface::class,
            ProductDownloadWriteServiceInterface::class,
            ProductDownloadReadServiceInterface::class,
        ];
    }
    
    
    /**
     * @inheritcDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ProductDownloadRepositoryInterface::class, ProductDownloadRepository::class)
            ->addArgument(\Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository::class);
        
        $this->application->registerShared(ProductDownloadFilterServiceInterface::class,
                                           ProductDownloadFilterService::class)
            ->addArgument(ProductDownloadFilterService::class);
        
        $this->application->registerShared(ProductDownloadWriteServiceInterface::class,
                                           ProductDownloadWriteService::class)
            ->addArgument(ProductDownloadWriteService::class);
        
        $this->application->registerShared(ProductDownloadReadServiceInterface::class,
                                           ProductDownloadReadService::class)
            ->addArgument(ProductDownloadReadService::class);
        
        $this->application->registerShared(ProductDownloadsOptionOperationPermitter::class)
            ->addArgument(\Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadsOptionOperationPermitter::class);
        
        $this->application->registerShared(ProductDownloadsImageListOperationPermitter::class)
            ->addArgument(\Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadsImageListOperationPermitter::class);
        
        $this->application->registerShared(ProductDownloadRequestParser::class)
            ->addArgument(ProductDownloadFactory::class);
        $this->application->registerShared(ProductDownloadRequestValidator::class);
    }
}