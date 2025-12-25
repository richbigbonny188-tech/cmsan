<?php
/* --------------------------------------------------------------
   Pagination.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects;

/**
 * Class Pagination
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects
 */
class Pagination
{
    /**
     * Pagination constructor.
     *
     * @param int $currentPage
     * @param int $itemsPerPage
     */
    public function __construct(private int $currentPage, private int $itemsPerPage)
    {
    }
    
    
    /**
     * @return int
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }
    
    
    /**
     * @return int
     */
    public function itemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
    
    
    /**
     * Calculates offset for mysql limit statement.
     *
     * @return int
     */
    public function mysqlOffset(): int
    {
        return $this->currentPage <= 1 ? 0 : ($this->currentPage - 1) * $this->itemsPerPage;
    }
}