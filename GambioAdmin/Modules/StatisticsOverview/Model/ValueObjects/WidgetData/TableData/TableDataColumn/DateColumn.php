<?php
/*--------------------------------------------------------------
   DateColumn.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn;
use Webmozart\Assert\Assert;

/**
 * Class representing a date table column.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn
 */
class DateColumn implements TableDataColumn
{
    /**
     * Type name.
     */
    private const TYPE = "date";
    
    /**
     * Title.
     *
     * @var string
     */
    private $title;
    
    /**
     * Field name.
     *
     * @var string
     */
    private $field;
    
    /**
     * Input date format.
     *
     * @var string
     */
    private $inputDateFormat;
    
    /**
     * Output date format.
     *
     * @var string
     */
    private $outputDateFormat;
    
    /**
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param string $title            Title.
     * @param string $field            Field name.
     * @param string $inputDateFormat  Input date format.
     * @param string $outputDateFormat Output date format.
     */
    private function __construct(string $title, string $field, string $inputDateFormat, string $outputDateFormat)
    {
        Assert::stringNotEmpty($title);
        Assert::stringNotEmpty($field);
        Assert::stringNotEmpty($inputDateFormat);
        Assert::stringNotEmpty($outputDateFormat);
        
        $this->title            = $title;
        $this->field            = $field;
        $this->inputDateFormat  = $inputDateFormat;
        $this->outputDateFormat = $outputDateFormat;
        $this->type             = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param string $title            Title.
     * @param string $field            Field name.
     * @param string $inputDateFormat  Input date format.
     * @param string $outputDateFormat Output date format.
     *
     * @return DateColumn Instance.
     */
    public static function create(string $title, string $field, string $inputDateFormat, string $outputDateFormat): self
    {
        return new self($title, $field, $inputDateFormat, $outputDateFormat);
    }
    
    
    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return $this->title;
    }
    
    
    /**
     * @inheritDoc
     */
    public function field(): string
    {
        return $this->field;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
    
    
    /**
     * Return input date format.
     *
     * @return string Input date format.
     */
    public function inputDateFormat(): string
    {
        return $this->inputDateFormat;
    }
    
    
    /**
     * Return output date format.
     *
     * @return string Output date format.
     */
    public function outputDateFormat(): string
    {
        return $this->outputDateFormat;
    }
}