<?php
/* --------------------------------------------------------------
   Pagination.php 2020-10-19
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
 * Interface Pagination
 *
 * @package Gambio\Core\Filter
 */
interface Pagination
{
    public const DEFAULT_LIMIT = 25;
    
    public const DEFAULT_OFFSET = 0;
    
    public const DEFAULT_PAGE = 0;
    
    public const DEFAULT_PER_PAGE = 25;
    
    
    /**
     * @return int
     */
    public function limit(): int;
    
    
    /**
     * @return int
     */
    public function offset(): int;
}