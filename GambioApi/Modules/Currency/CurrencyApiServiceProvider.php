<?php
/*--------------------------------------------------------------
   ImageListApiServiceProvider.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Currency;

use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyReadService as CurrencyReadServiceInterface;
use Gambio\Api\Modules\Currency\App\Actions\FetchAllCurrenciesAction;
use Gambio\Api\Modules\Currency\App\Actions\FetchSpecificCurrencyAction;
use Gambio\Api\Modules\Currency\App\CurrencyApiRequestParser;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class ImageListApiServiceProvider
 *
 * @package Gambio\Api\Modules\Currency
 * @codeCoverageIgnore
 */
class CurrencyApiServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            FetchAllCurrenciesAction::class,
            FetchSpecificCurrencyAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CurrencyApiRequestParser::class);
        
        $this->application->registerShared(FetchAllCurrenciesAction::class)
            ->addArgument(CurrencyFilterServiceInterface::class)
            ->addArgument(CurrencyApiRequestParser::class);
        
        $this->application->registerShared(FetchSpecificCurrencyAction::class)
            ->addArgument(CurrencyReadServiceInterface::class);
    }
}