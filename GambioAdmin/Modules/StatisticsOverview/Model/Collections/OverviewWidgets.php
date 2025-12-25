<?php
/*--------------------------------------------------------------
   Schemes.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\OverviewWidget;
use IteratorAggregate;

/**
 * Class representing a collection of widgets.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections
 */
class OverviewWidgets implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var OverviewWidget[]
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
     * @param OverviewWidget ...$widgets Values.
     *
     * @return OverviewWidgets Instance.
     */
    public static function create(OverviewWidget ...$widgets): self
    {
        return new self($widgets);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
}