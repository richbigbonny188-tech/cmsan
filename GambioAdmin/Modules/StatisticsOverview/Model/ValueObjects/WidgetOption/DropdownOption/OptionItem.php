<?php
/*--------------------------------------------------------------
   OptionItem.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems\ItemTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem\ItemValue;

/**
 * Class representing a dropdown item.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption
 */
class OptionItem
{
    /**
     * Value.
     *
     * @var ItemValue
     */
    private $value;
    
    /**
     * Multilingual titles.
     *
     * @var ItemTitles
     */
    private $titles;
    
    
    /**
     * Constructor.
     *
     * @param ItemValue  $value  Values.
     * @param ItemTitles $titles Multilingual titles.
     */
    private function __construct(ItemValue $value, ItemTitles $titles)
    {
        $this->value  = $value;
        $this->titles = $titles;
    }
    
    
    /**
     * Create instance.
     *
     * @param ItemValue  $value  Values.
     * @param ItemTitles $titles Multilingual titles.
     *
     * @return OptionItem Instance.
     */
    public static function create(ItemValue $value, ItemTitles $titles): self
    {
        return new self($value, $titles);
    }
    
    
    /**
     * Return value.
     *
     * @return ItemValue Value.
     */
    public function value(): ItemValue
    {
        return $this->value;
    }
    
    
    /**
     * Return titles.
     *
     * @return ItemTitles Titles.
     */
    public function titles(): ItemTitles
    {
        return $this->titles;
    }
}