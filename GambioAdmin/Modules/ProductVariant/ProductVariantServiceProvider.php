<?php
/*--------------------------------------------------------------
   ProductVariantServiceProvider.php 2022-02-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\ProductVariant;

use Gambio\Admin\Modules\ProductVariant\App as Legacy;
use Gambio\Admin\Modules\Product\Services as Product;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services as Submodule;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsFilterService as ProductVariantsFilterServiceInterface;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsGenerationService as ProductVariantsGenerationServiceInterface;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsRepository as ProductVariantsRepositoryInterface;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsWriteService as ProductVariantsWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;

/**
 * Class ProductVariantServiceProvider
 *
 * @package Gambio\Admin\Modules\Variants
 * @codeCoverageIgnore
 */
class ProductVariantServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ProductVariantsFilterServiceInterface::class,
            ProductVariantsReadServiceInterface::class,
            ProductVariantsWriteServiceInterface::class,
            ProductVariantsRepositoryInterface::class,
            ProductVariantsGenerationServiceInterface::class,
            ProductVariantFactory::class,
            Services\RecalculateProductVariantPriceService::class,
        ];
    }


    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->register(Services\ProductVariantFactory::class);

        $this->application->register(ProductVariantsFilterServiceInterface::class, Legacy\ProductVariantsFilterService::class)
            ->addArgument(Product\ProductVariantsFilterService::class);

        $this->application->register(ProductVariantsReadServiceInterface::class, Legacy\ProductVariantsReadService::class)
            ->addArgument(Product\ProductVariantsReadService::class);

        $this->application->register(ProductVariantsWriteServiceInterface::class, Legacy\ProductVariantsWriteService::class)
            ->addArgument(Product\ProductVariantsWriteService::class);

        $this->application->register(ProductVariantsRepositoryInterface::class, Legacy\ProductVariantsRepository::class)
            ->addArgument(Submodule\ProductVariantsRepository::class);

        $this->application->register(ProductVariantsGenerationServiceInterface::class, Legacy\ProductVariantsGenerationService::class)
            ->addArgument(Submodule\ProductVariantsGenerationService::class)
            ->addArgument(Services\ProductVariantFactory::class);

        $this->application->register(Services\RecalculateProductVariantPriceService::class, Legacy\RecalculateProductVariantPriceService::class)
            ->addArgument(Submodule\ProductVariantsGenerationService::class);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
    }
}