<?php
/* --------------------------------------------------------------
 MenuOrderingTrait.php 2020-02-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cache;

trait MenuSortTrait
{
    private function sortCallback(): callable
    {
        return static function (Sortable $a, Sortable $b): int {
            $aSort = $a->sortOrder();
            $bSort = $b->sortOrder();
            
            if ($aSort === $bSort) {
                return 0;
            }
            
            return $aSort < $bSort ? -1 : 1;
        };
    }
}