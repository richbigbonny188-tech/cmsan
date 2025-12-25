<?php
/* --------------------------------------------------------------
   DHLReturnsServiceProvider.php 2021-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionLabelList;
use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionMakeLabel;
use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionOrderData;
use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionOverview;
use Gambio\Admin\Modules\DHLReturns\App\Data\CountriesFacade;
use Gambio\Admin\Modules\DHLReturns\App\Data\OrderDataFacade;
use Gambio\Admin\Modules\DHLReturns\Services\DHLReturnsService;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceReadService;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFactory;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GuzzleHttp\Client;
use Gambio\Admin\Modules\DHLReturns\Services\DHLConfigurationService;

class DHLReturnsServiceProvider extends AbstractServiceProvider
{
    public function provides(): array
    {
        return [
            ActionOverview::class,
            ActionOrderData::class,
            ActionMakeLabel::class,
            ActionLabelList::class,
        ];
    }
    
    
    public function register(): void
    {
        $this->application->registerShared(Client::class);
        $this->application->registerShared(DHLConfigurationService::class)->addArgument(ConfigurationService::class);
        $this->application->registerShared(DHLReturnsService::class)
            ->addArgument(Client::class)
            ->addArgument(DHLConfigurationService::class);
        $this->application->registerShared(ActionOverview::class)
            ->addArgument(DHLConfigurationService::class)
            ->addArgument(CountriesFacade::class);
        $this->application->registerShared(ActionOrderData::class)->addArgument(OrderDataFacade::class);
        $this->application->registerShared(ActionMakeLabel::class)
            ->addArgument(DHLConfigurationService::class)
            ->addArgument(DHLReturnsService::class)
            ->addArgument(Path::class)
            ->addArgument(TrackingCodeWriteService::class)
            ->addArgument(TrackingCodeFactory::class)
            ->addArgument(ParcelServiceReadService::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(ActionLabelList::class)->addArgument(Path::class);
        
        $this->application->registerShared(CountriesFacade::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(OrderDataFacade::class)
            ->addArgument(Connection::class);
    }
}
