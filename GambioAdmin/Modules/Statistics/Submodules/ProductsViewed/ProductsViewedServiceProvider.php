<?php
/* --------------------------------------------------------------
   ProductsViewedServiceProvider.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data\ViewedProductsFactory;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data\ViewedProductsReader;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data\ViewedProductsRepository;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\ProductsViewCountService;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service\ProductStatisticsService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class ProductsViewedServiceProvider
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed
 */
class ProductsViewedServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            App\Actions\ViewedProductsStatistic::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(App\Actions\ViewedProductsStatistic::class)
            ->addArgument(ProductStatisticsService::class)
            ->addArgument(AdminMenuService::class);
        
        $this->application->registerShared(ProductStatisticsService::class, ProductsViewCountService::class)
            ->addArgument(ViewedProductsRepository::class);
        $this->application->registerShared(ViewedProductsRepository::class)
            ->addArgument(ViewedProductsReader::class)
            ->addArgument(ViewedProductsFactory::class);
        $this->application->registerShared(ViewedProductsReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ViewedProductsFactory::class);
    }
}