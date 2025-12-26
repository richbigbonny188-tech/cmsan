<?php
/*--------------------------------------------------------------
   FetchCustomerStatisticsAction.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data\Actions\JSON;

use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Currency\Services\Exceptions\InvalidCurrencyArgumentException;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsReadService as CustomerStatisticsReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\Exceptions\CustomerNotFoundException;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchCustomerStatisticsAction
 *
 * @package Gambio\Admin\Modules\CustomerStatistict\App\Actions
 * @codeCoverageIgnore
 */
class FetchCustomerStatisticsAction extends AbstractAction
{
    private CustomerStatisticsReadServiceInterface $customerStatisticsReadService;
    private CurrencyFilterServiceInterface         $currencyFilterService;
    
    
    /**
     * @param CustomerStatisticsReadServiceInterface $customerStatisticsReadService
     * @param CurrencyFilterServiceInterface         $currencyFilterService
     */
    public function __construct(
        CustomerStatisticsReadServiceInterface $customerStatisticsReadService,
        CurrencyFilterServiceInterface         $currencyFilterService
    ) {
        $this->customerStatisticsReadService = $customerStatisticsReadService;
        $this->currencyFilterService         = $currencyFilterService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $customerId      = (int)$request->getAttribute('customerId');
            $statistics      = $this->customerStatisticsReadService->getCustomerStatistics($customerId)->toArray();
            $defaultCurrency = $this->currencyFilterService->filterCurrencies(['isDefault' => 'true'])->toArray()[0];
            
            $responseData = array_merge(
                $statistics,
                ['currency' => $defaultCurrency]
            );
            
            return $response->withJson($responseData);
        } catch (CustomerNotFoundException|InvalidCurrencyArgumentException $exception) {
            return $response->withStatus(404)->withJson(['error' => $exception->getMessage()]);
        }
    }
}