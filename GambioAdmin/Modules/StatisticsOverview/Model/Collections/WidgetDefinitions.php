<?php
/*--------------------------------------------------------------
   WidgetDefinitions.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use IteratorAggregate;

/**
 * Class representing a collection of widget definitions.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections
 */
class WidgetDefinitions implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var WidgetDefinition[]
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
     * @param WidgetDefinition ...$widgets Values.
     *
     * @return WidgetDefinitions Instance.
     */
    public static function create(WidgetDefinition ...$widgets): self
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