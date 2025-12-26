<?php
/*--------------------------------------------------------------
   CountryServiceProvider.php 2022-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Country\App\Actions\FetchActiveCountriesAction;
use Gambio\Admin\Modules\Country\App\Actions\FetchAllCountriesAction;
use Gambio\Admin\Modules\Country\App\CountryReadService;
use Gambio\Admin\Modules\Country\App\CountryRepository;
use Gambio\Admin\Modules\Country\App\Data\CountryMapper;
use Gambio\Admin\Modules\Country\App\Data\CountryReader;
use Gambio\Admin\Modules\Country\Services\CountryFactory;
use Gambio\Admin\Modules\Country\Services\CountryReadService as CountryReadServiceInterface;
use Gambio\Admin\Modules\Country\Services\CountryRepository as CountryRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class CountryServiceProvider
 *
 * @package Gambio\Admin\Modules\Country
 * @codeCoverageIgnore
 */
class CountryServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CountryReadServiceInterface::class,
            CountryRepositoryInterface::class,
            FetchActiveCountriesAction::class,
            FetchAllCountriesAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CountryMapper::class);
        $this->application->registerShared(CountryFactory::class);
        $this->application->registerShared(CountryReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(CountryRepositoryInterface::class, CountryRepository::class)
            ->addArgument(CountryReader::class)
            ->addArgument(CountryMapper::class);
        
        $this->application->registerShared(CountryReadServiceInterface::class, CountryReadService::class)
            ->addArgument(CountryRepositoryInterface::class)
            ->addArgument(CountryFactory::class);
        
        $this->application->registerShared(FetchActiveCountriesAction::class)
            ->addArgument(CountryReadServiceInterface::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(FetchAllCountriesAction::class)
            ->addArgument(CountryReadServiceInterface::class)
            ->addArgument(UserPreferences::class);
    }
}