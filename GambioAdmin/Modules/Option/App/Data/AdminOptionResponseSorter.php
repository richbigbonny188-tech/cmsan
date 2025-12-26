<?php
/*--------------------------------------------------------------
   AdminOptionResponseSorter.php 2021-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Data;

/**
 * Class AdminOptionResponseSorter
 * @package Gambio\Admin\Modules\Option\App\Data
 */
class AdminOptionResponseSorter
{
    /**
     * sorts options and its values by the defined sort order
     *
     * @param array $options
     *
     * @return array
     */
    public function sortOptions(array $options): array
    {
        $options = array_map([$this, 'sortOptionValues'], $options);
        usort($options, [$this, 'sortArrayBySortOrder']);
        
        return $options;
    }
    
    
    /**
     * @param array $option
     *
     * @return array
     */
    private function sortOptionValues(array $option): array
    {
        usort($option['values'], [$this, 'sortArrayBySortOrder']);
        
        return $option;
    }
    
    
    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    private function sortArrayBySortOrder(array $a, array $b): int
    {
        return (int)$a['sortOrder'] <=> (int)$b['sortOrder'];
    }
}