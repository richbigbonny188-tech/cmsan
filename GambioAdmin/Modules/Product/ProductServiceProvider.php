<?php
/*--------------------------------------------------------------
   ProductServiceProvider.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product;

use Gambio\Admin\Modules\Product\App\AdditionalOptionFilterService;
use Gambio\Admin\Modules\Product\App\AdditionalOptionReadService;
use Gambio\Admin\Modules\Product\App\AdditionalOptionWriteService;
use Gambio\Admin\Modules\Product\App\ProductDownloadFilterService;
use Gambio\Admin\Modules\Product\App\ProductDownloadReadService;
use Gambio\Admin\Modules\Product\App\ProductDownloadWriteService;
use Gambio\Admin\Modules\Product\App\ProductVariantsFilterService;
use Gambio\Admin\Modules\Product\App\ProductVariantsReadService;
use Gambio\Admin\Modules\Product\App\ProductVariantsWriteService;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionFilterService as AdditionalOptionFilterServiceProxyInterface;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionReadService as AdditionalOptionReadServiceProxyInterface;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionWriteService as AdditionalOptionWriteServiceProxyInterface;
use Gambio\Admin\Modules\Product\Services\ProductDownloadFilterService as ProductDownloadFilterServiceProxyInterface;
use Gambio\Admin\Modules\Product\Services\ProductDownloadReadService as ProductDownloadReadServiceProxyInterface;
use Gambio\Admin\Modules\Product\Services\ProductDownloadWriteService as ProductDownloadWriteServiceProxyInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterService as SubmoduleAdditionalOptionFilterService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as SubmoduleAdditionalOptionReadService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService as SubmoduleAdditionalOptionWriteService;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFilterService as SubmoduleProductDownloadFilterService;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadReadService as SubmoduleProductDownloadReadService;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadWriteService as SubmoduleProductDownloadWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Admin\Modules\Product\Services\ProductVariantsFilterService as ProductVariantsFilterServiceProxyInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceProxyInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService as ProductVariantsWriteServiceProxyInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsFilterService as SubmoduleProductVariantsFilterService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService as SubmoduleProductVariantsReadService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsWriteService as SubmoduleProductVariantsWriteService;

/**
 * Class ProductServiceProvider
 *
 * @package Gambio\Admin\Modules\Product
 */
class ProductServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AdditionalOptionFilterServiceProxyInterface::class,
            AdditionalOptionReadServiceProxyInterface::class,
            AdditionalOptionWriteServiceProxyInterface::class,
            ProductDownloadFilterServiceProxyInterface::class,
            ProductDownloadReadServiceProxyInterface::class,
            ProductDownloadWriteServiceProxyInterface::class,
            ProductVariantsFilterServiceProxyInterface::class,
            ProductVariantsReadServiceProxyInterface::class,
            ProductVariantsWriteServiceProxyInterface::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerAdditionalOptionProxies();
        $this->registerProductDownloadProxies();
        $this->registerVariantProxies();
    }
    
    
    private function registerAdditionalOptionProxies(): void
    {
        $this->application->register(AdditionalOptionFilterServiceProxyInterface::class,
                                     AdditionalOptionFilterService::class)
            ->addArgument(SubmoduleAdditionalOptionFilterService::class);
        
        $this->application->register(AdditionalOptionReadServiceProxyInterface::class,
                                     AdditionalOptionReadService::class)
            ->addArgument(SubmoduleAdditionalOptionReadService::class);
        
        $this->application->register(AdditionalOptionWriteServiceProxyInterface::class,
                                     AdditionalOptionWriteService::class)
            ->addArgument(SubmoduleAdditionalOptionWriteService::class);
    }
    
    
    private function registerProductDownloadProxies(): void
    {
        $this->application->register(ProductDownloadFilterServiceProxyInterface::class,
                                     ProductDownloadFilterService::class)
            ->addArgument(SubmoduleProductDownloadFilterService::class);
        
        $this->application->register(ProductDownloadReadServiceProxyInterface::class,
                                     ProductDownloadReadService::class)
            ->addArgument(SubmoduleProductDownloadReadService::class);
        
        $this->application->register(ProductDownloadWriteServiceProxyInterface::class,
                                     ProductDownloadWriteService::class)
            ->addArgument(SubmoduleProductDownloadWriteService::class);
    }
    
    
    private function registerVariantProxies(): void
    {
        $this->application->register(ProductVariantsFilterServiceProxyInterface::class,
                                     ProductVariantsFilterService::class)
            ->addArgument(SubmoduleProductVariantsFilterService::class);
        
        $this->application->register(ProductVariantsReadServiceProxyInterface::class,
                                     ProductVariantsReadService::class)
            ->addArgument(SubmoduleProductVariantsReadService::class);
        
        $this->application->register(ProductVariantsWriteServiceProxyInterface::class,
                                     ProductVariantsWriteService::class)
            ->addArgument(SubmoduleProductVariantsWriteService::class);
    }
}