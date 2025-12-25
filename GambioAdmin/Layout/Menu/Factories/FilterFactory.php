<?php
/* --------------------------------------------------------------
 FilterFactory.php 2020-01-31
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 31 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Factories;

use Gambio\Admin\Layout\Menu\Filter\FilterCondition;
use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterFactory as FilterFactoryInterface;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use InvalidArgumentException;

/**
 * Class FilterFactory
 * @package Gambio\Admin\Layout\Menu\Filter
 */
class FilterFactory implements FilterFactoryInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];
    
    /**
     * @var bool
     */
    private $debug = false;
    
    
    /**
     * @inheritDoc
     */
    public function addFilter(string $filterName, FilterInterface $filter): void
    {
        $this->filters[$filterName] = $filter;
    }
    
    
    /**
     * @inheritDoc
     */
    public function create(FilterCondition $condition): FilterInterface
    {
        $filterName = $condition->filter();
        $filter     = $this->tryGetFilter($filterName);
        
        if ($filter) {
            return $filter;
        }
        if ($this->debug) {
            throw new InvalidArgumentException("No filter with name ({$condition->filter()}) found");
        }
        
        return $this->failFallback();
    }
    
    
    /**
     * Enables debugging mode.
     */
    public function debug(): void
    {
        $this->debug = true;
    }
    
    
    /**
     * Checks if a filter with $filterName is available.
     *
     * @param string $filterName
     *
     * @return FilterInterface
     */
    private function tryGetFilter(string $filterName): ?FilterInterface
    {
        return $this->filters[$filterName] ?? null;
    }
    
    
    /**
     * Fallback if no filter was found.
     *
     * This function returns a anonymous class that implements the FilterInterface and
     * return true when using the ::check() method.
     *
     * @return FilterInterface
     */
    private function failFallback(): FilterInterface
    {
        return new class implements FilterInterface {
            public function check(FilterConditionArguments $condition): bool
            {
                return false;
            }
        };
    }
}
