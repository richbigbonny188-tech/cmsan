<?php
/*--------------------------------------------------------------
   TextDataFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TextData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TextData\TextDataValue;

/**
 * Class representing a text data factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory
 */
class TextDataFactory
{
    /**
     * Return text data.
     *
     * @param TextDataValue $text Text.
     *
     * @return TextData Text data.
     */
    public function createTextData(TextDataValue $text): TextData
    {
        return TextData::create($text);
    }
    
    
    /**
     * Return value.
     *
     * @param string $value Value.
     *
     * @return TextDataValue Value.
     */
    public function createValue(string $value): TextDataValue
    {
        return TextDataValue::create($value);
    }
}