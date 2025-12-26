<?php
/*--------------------------------------------------------------
   DropdownOption.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\OptionTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem;
use Webmozart\Assert\Assert;

/**
 * Class representing a dropdown option.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption
 */
class DropdownOption implements WidgetOption
{
    /**
     * Type name.
     */
    private const TYPE = "dropdown";
    
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
     * @var string
     */
    private $value;
    
    /**
     * Items.
     *
     * @var OptionItems
     */
    private $items;
    
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
     * @param string       $value  Value.
     * @param OptionItems  $items  Items.
     * @param OptionTitles $titles Multilingual titles.
     */
    private function __construct(
        OptionId $id,
        string $value,
        OptionItems $items,
        OptionTitles $titles
    ) {
        Assert::stringNotEmpty($value);
        Assert::inArray($value,
                        array_map(function (OptionItem $item) {
                            return $item->value()->value();
                        },
                            iterator_to_array($items)));
        
        $this->id     = $id;
        $this->titles = $titles;
        $this->value  = $value;
        $this->items  = $items;
        $this->type   = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param OptionId     $id     ID.
     * @param string       $value  Value.
     * @param OptionItems  $items  Items.
     * @param OptionTitles $titles Multilingual titles.
     *
     * @return DropdownOption Instance.
     */
    public static function create(
        OptionId $id,
        string $value,
        OptionItems $items,
        OptionTitles $titles
    ): self {
        return new self($id, $value, $items, $titles);
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
    public function value(): string
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
    
    
    /**
     * Return dropdown items.
     *
     * @return OptionItems Items.
     */
    public function items(): OptionItems
    {
        return $this->items;
    }
}