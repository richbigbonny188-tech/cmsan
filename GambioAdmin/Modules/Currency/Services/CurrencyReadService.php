<?php
/*--------------------------------------------------------------
   CurrencyReadService.php 2022-06-15
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
use Gambio\Admin\Modules\Currency\Services\Exceptions\CurrencyDoesNotExistException;
use Gambio\Admin\Modules\Currency\Services\Exceptions\InvalidCurrencyArgumentException;

/**
 * Interface CurrencyReadService
 *
 * @package Gambio\Admin\Modules\Currency\Services
 */
interface CurrencyReadService
{
    /**
     * Returns a specific currency based on the given currency ID.
     *
     * @param int $currencyId
     *
     * @return Currency
     *
     * @throws CurrencyDoesNotExistException
     * @throws InvalidCurrencyArgumentException
     */
    public function getCurrencyById(int $currencyId): Currency;
    
    
    /**
     * Returns a collection of all currencies.
     *
     * @return Currencies
     * @throws InvalidCurrencyArgumentException
     */
    public function getAllCurrencies(): Currencies;
}