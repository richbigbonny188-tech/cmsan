<?php
/*--------------------------------------------------------------
   CurrencyFilters.php 2022-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Model\Filter;

use Gambio\Core\Filter\SqlFilters;

/**
 * Class CurrencyFilters
 *
 * @package Gambio\Admin\Modules\Currency\Model\Filter
 * @codeCoverageIgnore
 */
class CurrencyFilters extends SqlFilters
{
    
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'                 => 'currencies_id',
            'name'               => 'title',
            'code'               => 'code',
            'symbols.left'       => 'symbol_left',
            'symbols.right'      => 'symbol_right',
            'value'              => 'value',
            'decimalSeparator'   => 'decimal_point',
            'thousandsSeparator' => 'thousands_point',
            'decimalPlaces'      => 'decimal_places',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForLikeOperation(): array
    {
        return [
            'name',
            'code',
            'symbols.left',
            'symbols.right',
            'decimalSeparator',
            'thousandsSeparator',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForNumericOperations(): array
    {
        return [
            'id',
            'value',
            'decimalPlaces',
        ];
    }
}