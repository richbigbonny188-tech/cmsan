<?php
/* --------------------------------------------------------------
 FilterInterface.php 2020-01-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 29 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Filter;

/**
 * Interface FilterInterface
 * @package Gambio\Admin\Layout\Menu\Filter
 */
interface FilterInterface
{
    /**
     * Performs a check, so a later process can filter with that result.
     *
     * @param FilterConditionArguments $condition
     *
     * @return bool
     */
    public function check(FilterConditionArguments $condition): bool;
}