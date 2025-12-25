<?php
/*
 * --------------------------------------------------------------
 *   ProductListingService.php 2022-01-11
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App;

use Gambio\Shop\Modules\ProductListing\App\Event\ListingCollected;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;
use Gambio\Shop\Modules\ProductListing\Service\ListingExtender;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;
use Gambio\Shop\Modules\ProductListing\Service\ListingRepository;
use Gambio\Shop\Modules\ProductListing\Service\ListingService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ProductListingService
 *
 * @package Gambio\Shop\Modules\ProductListing\App
 */
class ProductListingService implements ListingService
{
    private EventDispatcherInterface $eventDispatcher;
    
    private ListingRepository $repository;
    
    /**
     * @var ListingExtender[]
     */
    private array $extenders = [];
    
    
    /**
     * ProductListingService constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param ListingRepository        $repository
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ListingRepository $repository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->repository      = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getListing(ListingFilter $filter, ListingPagination $pagination): Listing
    {
        $listing = $this->repository->getListing($filter->getProductIds(),
                                                 $pagination,
                                                 $filter->getSettings(),
                                                 $filter->getSortOrder());
        
        $this->eventDispatcher->dispatch(new ListingCollected($listing));
        
        if ($event = $filter->getListingEvent($listing)) {
            $this->eventDispatcher->dispatch($event);
        }
        
        foreach ($this->extenders as $extender) {
            $extender->extend($listing, $filter);
        }
        
        return $listing;
    }
    
    
    /**
     * Registers a new listing extender.
     * Listing extenders can be used to extend the processed listing.
     *
     * @param ListingExtender $extender
     *
     * @return void
     */
    public function registerExtender(ListingExtender $extender): void
    {
        $this->extenders[get_class($extender)] = $extender;
    }
}