<?php
/* --------------------------------------------------------------
   ListingPagination.php 2022-01-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemId
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingPagination
{
    private int $currentPage;
    
    private int $itemsPerPage;
    
    
    /**
     * ListingPagination constructor.
     *
     * @param int $currentPage
     * @param int $itemsPerPage
     */
    public function __construct(int $currentPage, int $itemsPerPage)
    {
        $this->currentPage  = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
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
    public function mySqlOffset(): int
    {
        return $this->currentPage <= 1 ? 0 : ($this->currentPage - 1) * $this->itemsPerPage;
    }
}