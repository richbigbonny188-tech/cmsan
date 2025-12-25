<?php
/*--------------------------------------------------------------
   NumberRowField.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField;
use Webmozart\Assert\Assert;

/**
 * Class representing a number table row field.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField
 */
class NumberRowField implements RowField
{
    /**
     * Name.
     *
     * @var string
     */
    private $name;
    
    /**
     * Value.
     *
     * @var float
     */
    private $value;
    
    
    /**
     * Constructor.
     *
     * @param string $name  Name.
     * @param float  $value Value.
     */
    private function __construct(string $name, float $value)
    {
        Assert::stringNotEmpty($name);
        
        $this->name  = $name;
        $this->value = $value;
    }
    
    
    /**
     * Create instance.
     *
     * @param string $name  Name.
     * @param float  $value Value.
     *
     * @return NumberRowField Instance.
     */
    public static function create(string $name, float $value): self
    {
        return new self($name, $value);
    }
    
    
    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @inheritDoc
     */
    public function value(): float
    {
        return $this->value;
    }
}