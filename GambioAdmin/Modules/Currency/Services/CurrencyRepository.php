<?php
/*--------------------------------------------------------------
   CurrencyRepository.php 2022-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Services;

use Gambio\Admin\Modules\Currency\Model\Collections\Currencies;
use Gambio\Admin\Modules\Currency\Model\Currency;
use Gambio\Admin\Modules\Currency\Model\ValueObjects\CurrencyId;
use Gambio\Admin\Modules\Currency\Services\Exceptions\CurrencyDoesNotExistException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface CurrencyRepository
 *
 * @package Gambio\Admin\Modules\Currency\Services
 */
interface CurrencyRepository
{
    /**
     * Returns a filtered, sorted, paginated collection of currencies.
     *
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return Currencies
     */
    public function filterCurrencies(
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
    ): Currencies;
    
    
    /**
     * Returns the total count of filtered currencies.
     *
     * @param Filters $filters
     *
     * @return int
     */
    public function getCurrenciesTotalCount(Filters $filters): int;
    
    
    /**
     * Returns a specific currency based on the given currency ID.
     *
     * @param CurrencyId $currencyId
     *
     * @return Currency
     *
     * @throws CurrencyDoesNotExistException
     */
    public function getCurrencyById(CurrencyId $currencyId): Currency;
    
    
    /**
     * Returns a collection of all currencies.
     *
     * @return Currencies
     */
    public function getAllCurrencies(): Currencies;
}