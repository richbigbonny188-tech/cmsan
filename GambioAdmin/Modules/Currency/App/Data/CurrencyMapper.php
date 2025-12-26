<?php
/*--------------------------------------------------------------
   CurrencyMapper.php 2022-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\App\Data;

use Gambio\Admin\Modules\Currency\Model\Collections\Currencies;
use Gambio\Admin\Modules\Currency\Model\Currency;
use Gambio\Admin\Modules\Currency\Services\CurrencyFactory;

/**
 * Class CurrencyMapper
 *
 * @package Gambio\Admin\Modules\Currency\App\Data
 */
class CurrencyMapper extends CurrencyFactory
{
    /**
     * @param array ...$data
     *
     * @return Currencies
     */
    public function mapCurrencies(array ...$data): Currencies
    {
        $currencies = array_map([$this, 'mapCurrency'], $data);
        
        return $this->createCurrencies(...$currencies);
    }
    
    
    /**
     * @param array $data
     *
     * @return Currency
     */
    public function mapCurrency(array $data): Currency
    {
        $id                 = $this->createCurrencyId((int)$data['currencies_id']);
        $name               = $data['title'];
        $code               = $data['code'];
        $symbols            = $this->createCurrencySymbols($data['symbol_left'], $data['symbol_right']);
        $value              = (float)$data['value'];
        $decimalSeparator   = $data['decimal_point'];
        $thousandsSeparator = $data['thousands_point'];
        $decimalPlaces      = (int)$data['decimal_places'];
        $isDefault          = $data['is_default'] === 'true';
        
        return Currency::create($id,
                                $name,
                                $code,
                                $symbols,
                                $value,
                                $decimalSeparator,
                                $thousandsSeparator,
                                $decimalPlaces,
                                $isDefault);
    }
}