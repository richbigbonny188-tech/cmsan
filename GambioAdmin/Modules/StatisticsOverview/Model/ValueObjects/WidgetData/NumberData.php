<?php
/*--------------------------------------------------------------
   NumberData.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\NumberData\NumberDataValue;

/**
 * Class representing number data.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData
 */
class NumberData implements WidgetData
{
    /**
     * Type name.
     */
    private const TYPE = "number";
    
    /**
     * Value.
     *
     * @var NumberDataValue
     */
    private $number;
    
    /**
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param NumberDataValue $number Value.
     */
    private function __construct(NumberDataValue $number)
    {
        $this->number = $number;
        $this->type   = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param NumberDataValue $number Value.
     *
     * @return NumberData Instance.
     */
    public static function create(NumberDataValue $number): self
    {
        return new self($number);
    }
    
    
    /**
     * Return number.
     *
     * @return NumberDataValue Value.
     */
    public function number(): NumberDataValue
    {
        return $this->number;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
}