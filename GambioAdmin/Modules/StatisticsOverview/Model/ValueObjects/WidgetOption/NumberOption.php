<?php
/*--------------------------------------------------------------
   NumberOption.php 2022-05-13
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
 * Class representing a number option.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption
 */
class NumberOption implements WidgetOption
{
    /**
     * Type name.
     */
    private const TYPE = "number";
    
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
     * @var int
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
     * @param int          $value  Value.
     * @param OptionTitles $titles Multilingual titles.
     */
    private function __construct(OptionId $id, int $value, OptionTitles $titles)
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
     * @param int          $value  Value.
     * @param OptionTitles $titles Multilingual titles.
     *
     * @return NumberOption Instance.
     */
    public static function create(OptionId $id, int $value, OptionTitles $titles): self
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
    public function value(): int
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