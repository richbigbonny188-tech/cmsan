<?php
/*--------------------------------------------------------------
   ItemTitle.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem;

use Webmozart\Assert\Assert;

/**
 * Class representing the title of a map item.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem
 */
class ItemTitle
{
    /**
     * Value.
     *
     * @var string
     */
    private $value;
    
    
    /**
     * Constructor.
     *
     * @param string $value Value.
     */
    private function __construct(string $value)
    {
        Assert::stringNotEmpty($value);
        
        $this->value = $value;
    }
    
    
    /**
     * Create instance.
     *
     * @param string $item Value.
     *
     * @return ItemTitle Instance.
     */
    public static function create(string $item): self
    {
        return new self($item);
    }
    
    
    /**
     * Return value.
     *
     * @return string Value.
     */
    public function value(): string
    {
        return $this->value;
    }
}