<?php
/* --------------------------------------------------------------
 FilterConditionArguments.php 2020-01-31
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
 * Interface FilterConditionArguments
 * @package Gambio\Admin\Layout\Menu\Filter
 */
interface FilterConditionArguments
{
    /**
     * Arguments of filter condition.
     *
     * @return array
     */
    public function args(): array;
}