<?php
/*--------------------------------------------------------------
   SoldProductServiceProvider.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Html\PurchasedProductsIndexAction;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Json\FetchAllSoldProductsAction;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data\SoldProductMapper;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data\SoldProductReader;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\SoldProductReadService;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\SoldProductRepository;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductFactory;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductReadService as SoldProductReadServiceInterface;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductRepository as SoldProductRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class SoldProductServiceProvider
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct
 * @codeCoverageIgnore
 */
class SoldProductServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            SoldProductReadServiceInterface::class,
            FetchAllSoldProductsAction::class,
            PurchasedProductsIndexAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->register(SoldProductMapper::class);
        $this->application->register(SoldProductFactory::class);
        $this->application->register(SoldProductReader::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class);
        $this->application->register(SoldProductRepositoryInterface::class, SoldProductRepository::class)
            ->addArgument(SoldProductReader::class)
            ->addArgument(SoldProductMapper::class);
        $this->application->register(SoldProductReadServiceInterface::class, SoldProductReadService::class)
            ->addArgument(SoldProductFactory::class)
            ->addArgument(SoldProductRepositoryInterface::class);
        $this->application->register(FetchAllSoldProductsAction::class)
            ->addArgument(SoldProductReadServiceInterface::class);
        
        $this->application->register(PurchasedProductsIndexAction::class)->addArgument(SoldProductReadServiceInterface::class);
    }
}