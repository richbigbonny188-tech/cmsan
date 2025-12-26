<?php
/* --------------------------------------------------------------
   ProductListingRepository.php 2022-01-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPaginationMeta;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Service\ListingRepository;

/**
 * Class ListingItemId
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingRepository implements ListingRepository
{
    private ProductListingReader $reader;
    
    private ProductListingMapper $mapper;
    
    private ProductListingModelFactory $factory;
    
    
    /**
     * ProductListingRepository constructor.
     *
     * @param ProductListingReader       $reader
     * @param ProductListingMapper       $mapper
     * @param ProductListingModelFactory $factory
     */
    public function __construct(
        ProductListingReader       $reader,
        ProductListingMapper       $mapper,
        ProductListingModelFactory $factory
    ) {
        $this->reader  = $reader;
        $this->mapper  = $mapper;
        $this->factory = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getListing(
        ListingItemIds    $ids,
        ListingPagination $pagination,
        ListingSettings   $settings,
        ListingSortOrder  $sortOrder
    ): Listing {
        if ($ids->isEmpty()) {
            $meta = new ListingPaginationMeta($pagination->currentPage(), $pagination->itemsPerPage(), 0, 1);
            
            return new Listing($meta);
        }
        
        $rawData = $this->reader->fetch($ids, $pagination, $sortOrder, $settings);
        $items   = $this->mapper->map($rawData, $settings);
        
        $totalItems = $this->reader->fetchTotal($ids, $settings);
        $meta       = $this->factory->createPaginationMeta($totalItems, $pagination);
        
        return new Listing($meta, ...$items);
    }
}