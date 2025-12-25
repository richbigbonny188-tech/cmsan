<?php
/*--------------------------------------------------------------
   NumberDataFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\NumberData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\NumberData\NumberDataValue;

/**
 * Class representing a number data factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory
 */
class NumberDataFactory
{
    /**
     * Return number data.
     *
     * @param NumberDataValue $number Value.
     *
     * @return NumberData Number data.
     */
    public function createNumberData(NumberDataValue $number): NumberData
    {
        return NumberData::create($number);
    }
    
    
    /**
     * Return value.
     *
     * @param float $value Value.
     *
     * @return NumberDataValue Value.
     */
    public function createValue(float $value): NumberDataValue
    {
        return NumberDataValue::create($value);
    }
}