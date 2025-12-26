<?php
/*--------------------------------------------------------------
   CurrencyServiceProvider.php 2021-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Currency\App\CurrencyFilterService;
use Gambio\Admin\Modules\Currency\App\CurrencyReadService;
use Gambio\Admin\Modules\Currency\App\CurrencyRepository;
use Gambio\Admin\Modules\Currency\App\Data\CurrencyMapper;
use Gambio\Admin\Modules\Currency\App\Data\CurrencyReader;
use Gambio\Admin\Modules\Currency\Services\CurrencyFactory;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterFactory;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyReadService as CurrencyReadServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyRepository as CurrencyRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class CurrencyServiceProvider
 *
 * @package Gambio\Admin\Modules\Currency
 * @codeCoverageIgnore
 */
class CurrencyServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CurrencyRepositoryInterface::class,
            CurrencyReadServiceInterface::class,
            CurrencyFilterServiceInterface::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CurrencyReader::class)->addArgument(Connection::class);
        $this->application->registerShared(CurrencyMapper::class);
        $this->application->registerShared(CurrencyFactory::class);
        $this->application->registerShared(CurrencyFilterFactory::class);
        
        $this->application->registerShared(CurrencyRepositoryInterface::class, CurrencyRepository::class)
            ->addArgument(CurrencyReader::class)
            ->addArgument(CurrencyMapper::class);
        
        $this->application->registerShared(CurrencyReadServiceInterface::class, CurrencyReadService::class)
            ->addArgument(CurrencyRepositoryInterface::class)
            ->addArgument(CurrencyFactory::class);
        
        $this->application->registerShared(CurrencyFilterServiceInterface::class, CurrencyFilterService::class)
            ->addArgument(CurrencyFilterFactory::class)
            ->addArgument(CurrencyRepositoryInterface::class);
    }
}