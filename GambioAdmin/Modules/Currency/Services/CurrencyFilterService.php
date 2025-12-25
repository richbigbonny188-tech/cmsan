<?php
/*--------------------------------------------------------------
   CurrencyFilterService.php 2022-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Services;

use Gambio\Admin\Modules\Currency\Model\Collections\Currencies;
use Gambio\Admin\Modules\Currency\Services\Exceptions\InvalidCurrencyArgumentException;

/**
 * Interface CurrencyFilterService
 *
 * @package Gambio\Admin\Modules\Currency\Services
 */
interface CurrencyFilterService
{
    /**
     * Returns a filtered, sorted, paginated collection of currencies.
     *
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return Currencies
     * @throws InvalidCurrencyArgumentException
     */
    public function filterCurrencies(
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): Currencies;
    
    
    /**
     * Returns the total count of filtered currencies.
     *
     * @param array $filters
     *
     * @return int
     * @throws InvalidCurrencyArgumentException
     */
    public function getCurrenciesTotalCount(array $filters): int;
}