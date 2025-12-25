<?php
/* --------------------------------------------------------------
   Sorting.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

/**
 * Interface Sorting
 *
 * @package Gambio\Core\Filter
 */
interface Sorting
{
    public const SORTING_PATTERN = '/^(([-\+])?([a-zA-Z\._0-9]+)\,)*([-\+])?([a-zA-Z\._0-9]+)$/';
    
    
    /**
     * @return string|null
     */
    public function sorting(): ?string;
}