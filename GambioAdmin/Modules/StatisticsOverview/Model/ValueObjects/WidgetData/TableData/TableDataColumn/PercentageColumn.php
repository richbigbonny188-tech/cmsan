<?php
/*--------------------------------------------------------------
   PercentageColumn.php 2022-05-13
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
 * Class representing a percentage table column.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn
 */
class PercentageColumn implements TableDataColumn
{
    /**
     * Type name.
     */
    private const TYPE = "percentage";
    
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
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param string $title Title.
     * @param string $field Field name.
     */
    private function __construct(string $title, string $field)
    {
        Assert::stringNotEmpty($title);
        Assert::stringNotEmpty($field);
        
        $this->title = $title;
        $this->field = $field;
        $this->type  = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param string $title Title.
     * @param string $field Field name.
     *
     * @return PercentageColumn Instance.
     */
    public static function create(string $title, string $field): self
    {
        return new self($title, $field);
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
}