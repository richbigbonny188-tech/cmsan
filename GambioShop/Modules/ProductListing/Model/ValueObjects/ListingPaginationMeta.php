<?php
/* --------------------------------------------------------------
   ListingPaginationMeta.php 2022-01-07
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
 * Class ListingPaginationMeta
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingPaginationMeta
{
    private int $page;
    
    private int $perPage;
    
    private int $totalItems;
    
    private int $maxPage;
    
    
    /**
     * ListingPaginationMeta constructor.
     *
     * @param int $page
     * @param int $perPage
     * @param int $totalItems
     * @param int $maxPage
     */
    public function __construct(int $page, int $perPage, int $totalItems, int $maxPage)
    {
        $this->page       = $page;
        $this->perPage    = $perPage;
        $this->totalItems = $totalItems;
        $this->maxPage    = $maxPage;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'page'       => $this->page,
            'perPage'    => $this->perPage,
            'totalItems' => $this->totalItems,
            'maxPage'    => $this->maxPage,
        ];
    }
}