<?php
/*--------------------------------------------------------------
   CheckboxOption.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\OptionTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;

/**
 * Class representing a checkbox option.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption
 */
class CheckboxOption implements WidgetOption
{
    /**
     * Type name.
     */
    private const TYPE = "checkbox";
    
    /**
     * ID.
     *
     * @var OptionId
     */
    private $id;
    
    /**
     * Multilingual titles.
     *
     * @var OptionTitles
     */
    private $titles;
    
    /**
     * Value.
     *
     * @var bool
     */
    private $value;
    
    /**
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param OptionId     $id     ID.
     * @param bool         $value  Value.
     * @param OptionTitles $titles Multilingual titles.
     */
    private function __construct(OptionId $id, bool $value, OptionTitles $titles)
    {
        $this->id     = $id;
        $this->titles = $titles;
        $this->value  = $value;
        $this->type   = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param OptionId     $id     ID.
     * @param bool         $value  Value.
     * @param OptionTitles $titles Multilingual titles.
     *
     * @return CheckboxOption Instance.
     */
    public static function create(OptionId $id, bool $value, OptionTitles $titles): self
    {
        return new self($id, $value, $titles);
    }
    
    
    /**
     * @inheritDoc
     */
    public function id(): OptionId
    {
        return $this->id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function value(): bool
    {
        return $this->value;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
    
    
    /**
     * @inheritDoc
     */
    public function titles(): OptionTitles
    {
        return $this->titles;
    }
}