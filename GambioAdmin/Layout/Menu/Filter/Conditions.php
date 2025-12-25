<?php
/* --------------------------------------------------------------
 Conditions.php 2022-08-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Filter;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class Conditions
 * @package Gambio\Admin\Layout\Menu\Filter
 */
final class Conditions implements IteratorAggregate
{
    /**
     * @var Condition[]
     */
    private $conditions;
    
    
    /**
     * Conditions constructor.
     *
     * @param Condition ...$conditions
     */
    public function __construct(Condition ...$conditions)
    {
        $this->conditions = $conditions;
    }
    
    
    /**
     * @return Condition[]
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->conditions);
    }
}