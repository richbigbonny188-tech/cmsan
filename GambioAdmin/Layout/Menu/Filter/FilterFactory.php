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

namespace Gambio\Admin\Layout\Menu\Filter;

/**
 * Interface FilterFactory
 * @package Gambio\Admin\Layout\Menu\Filter
 */
interface FilterFactory
{
    /**
     * Adds a new filter to the factory.
     *
     * @param string          $filterName
     * @param FilterInterface $filter
     */
    public function addFilter(string $filterName, FilterInterface $filter): void;
    
    
    /**
     * Creates a new filter.
     *
     * @param FilterCondition $condition
     *
     * @return FilterInterface
     */
    public function create(FilterCondition $condition): FilterInterface;
}