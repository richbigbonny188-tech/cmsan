<?php
/*--------------------------------------------------------------
   TextRowField.php 2022-05-13
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
 * Class representing a text table row field.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField
 */
class TextRowField implements RowField
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
     * @var string
     */
    private $value;
    
    
    /**
     * Constructor.
     *
     * @param string $name  Name.
     * @param string $value Value.
     */
    private function __construct(string $name, string $value)
    {
        Assert::stringNotEmpty($name);
        Assert::stringNotEmpty($value);
        
        $this->name  = $name;
        $this->value = $value;
    }
    
    
    /**
     * Create instance.
     *
     * @param string $name  Name.
     * @param string $value Value.
     *
     * @return TextRowField Instance.
     */
    public static function create(string $name, string $value): self
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
    public function value(): string
    {
        return $this->value;
    }
}