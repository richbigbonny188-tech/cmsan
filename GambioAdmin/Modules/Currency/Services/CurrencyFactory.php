<?php
/*--------------------------------------------------------------
   CurrencyFactory.php 2022-06-08
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
use Gambio\Admin\Modules\Currency\Model\ValueObjects\CurrencySymbols;

/**
 * Class CurrencyFactory
 *
 * @package Gambio\Admin\Modules\Currency\Services
 */
class CurrencyFactory
{
    /**
     * @param int $id
     *
     * @return CurrencyId
     */
    public function createCurrencyId(int $id): CurrencyId
    {
        return CurrencyId::create($id);
    }
    
    
    /**
     * @param string $left
     * @param string $right
     *
     * @return CurrencySymbols
     */
    public function createCurrencySymbols(string $left, string $right): CurrencySymbols
    {
        return CurrencySymbols::create($left, $right);
    }
    
    
    /**
     * @param Currency ...$currency
     *
     * @return Currencies
     */
    public function createCurrencies(Currency ...$currency): Currencies
    {
        return Currencies::create(...$currency);
    }
}