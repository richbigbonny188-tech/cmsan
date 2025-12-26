<?php
/*--------------------------------------------------------------
   OptionItems.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem;
use IteratorAggregate;

/**
 * Class representing a collection of dropdown items.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption
 */
class OptionItems implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var OptionItem[]
     */
    private $values;
    
    
    /**
     * Constructor.
     *
     * @param array $values Values.
     */
    private function __construct(array $values)
    {
        $this->values = $values;
    }
    
    
    /**
     * Create instance.
     *
     * @param OptionItem ...$items Values.
     *
     * @return OptionItems Instance.
     */
    public static function create(OptionItem ...$items): self
    {
        return new self($items);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
}