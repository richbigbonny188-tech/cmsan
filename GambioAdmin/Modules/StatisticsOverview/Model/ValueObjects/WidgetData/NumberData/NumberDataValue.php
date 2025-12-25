<?php
/*--------------------------------------------------------------
   NumberDataValue.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\NumberData;

/**
 * Class representing the number value.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\NumberData
 */
class NumberDataValue
{
    /**
     * Value.
     *
     * @var float
     */
    private $value;
    
    
    /**
     * Constructor.
     *
     * @param float $value Value.
     */
    private function __construct(float $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * Create instance.
     *
     * @param float $value Value.
     *
     * @return NumberDataValue Instance.
     */
    public static function create(float $value): self
    {
        return new self($value);
    }
    
    
    /**
     * Return value.
     *
     * @return float Value.
     */
    public function value(): float
    {
        return $this->value;
    }
    
}