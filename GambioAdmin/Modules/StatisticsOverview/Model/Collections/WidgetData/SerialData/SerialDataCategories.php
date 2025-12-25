<?php
/*--------------------------------------------------------------
   SerialCategories.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataCategory;
use IteratorAggregate;

/**
 * Class representing the collection of serial categories.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData
 */
class SerialDataCategories implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var SerialDataCategory[]
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
     * @param SerialDataCategory ...$categories Values.
     *
     * @return SerialDataCategories Instance.
     */
    public static function create(SerialDataCategory ...$categories): self
    {
        return new self($categories);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->values);
    }
}