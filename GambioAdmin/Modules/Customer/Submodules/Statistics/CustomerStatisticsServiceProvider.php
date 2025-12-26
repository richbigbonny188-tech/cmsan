<?php
/*--------------------------------------------------------------
   CustomerStatisticsServiceProvider.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\App\CustomerStatisticsReadService;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\App\CustomerStatisticsRepository;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data\Actions\JSON\FetchCustomerStatisticsAction;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data\CustomerStatisticsMapper;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data\CustomerStatisticsReader;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsFactory;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsReadService as CustomerStatisticsReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsRepository as CustomerStatisticsRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class CustomerStatisticsServiceProvider
 *
 * @package Gambio\Admin\Modules\CustomerStatistics
 * @codeCoverageIgnore
 */
class CustomerStatisticsServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [CustomerStatisticsReadServiceInterface::class, FetchCustomerStatisticsAction::class];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerStatisticsReader::class)->addArgument(Connection::class);
        $this->application->registerShared(CustomerStatisticsFactory::class);
        $this->application->registerShared(CustomerStatisticsMapper::class);
        
        $this->application->registerShared(CustomerStatisticsRepositoryInterface::class,
                                           CustomerStatisticsRepository::class)
            ->addArgument(CustomerStatisticsMapper::class)
            ->addArgument(CustomerStatisticsReader::class);
        
        $this->application->registerShared(CustomerStatisticsReadServiceInterface::class,
                                           CustomerStatisticsReadService::class)
            ->addArgument(CustomerStatisticsFactory::class)
            ->addArgument(CustomerStatisticsRepositoryInterface::class);
        
        $this->application->registerShared(FetchCustomerStatisticsAction::class)
            ->addArgument(CustomerStatisticsReadServiceInterface::class)
            ->addArgument(CurrencyFilterServiceInterface::class);
    }
}