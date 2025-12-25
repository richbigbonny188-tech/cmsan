<?php
/*
 * --------------------------------------------------------------
 *   LastViewedFilter.php 2023-06-14
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2023 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Filters;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\App\Event\LastViewedCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;
use PDOStatement;

/**
 * Class LastViewedFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 *
 * This filter requires no buildStatement() method since it is provided by IO layer
 * @see     \LastViewedBoxThemeContentView::prepare_data()
 */
class LastViewedFilter implements ListingFilter
{
    use FilterUtilityTrait;
    
    private ListingSettings     $settings;
    private Connection          $connection;
    private ConfigurationFinder $configurationFinder;
    private int                 $lastViewedProductId;
    
    
    public function __construct(
        ListingSettings     $settings,
        Connection          $connection,
        ConfigurationFinder $configurationFinder,
        int                 $lastViewedProductId
    ) {
        $this->settings            = $settings;
        $this->connection          = $connection;
        $this->configurationFinder = $configurationFinder;
        $this->lastViewedProductId = $lastViewedProductId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductIds(): ListingItemIds
    {
        $cb = static function (array $item): ListingItemId {
            return new ListingItemId((int)$item['products_id']);
        };
        
        $result = $this->buildStatement();
        
        return new ListingItemIds(...array_map($cb, $result->fetchAllAssociative()));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSortOrder(): ListingSortOrder
    {
        return new ListingSortOrder(ListingSortValue::id(), ListingSortDirection::asc());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSettings(): ListingSettings
    {
        return $this->settings;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getListingEvent(Listing $listing): ?object
    {
        return new LastViewedCollected($listing);
    }
    
    
    /**
     * Builds a sql statement fetching ids for product that should appear by provided ID.
     * This method also takes group and fsk checks into account.
     *
     * @return Result
     * @see \LastViewedBoxThemeContentView::prepare_data()
     *
     */
    private function buildStatement(): Result
    {
        $qb = $this->connection->createQueryBuilder()->select('p.products_id')->from('products', 'p');
        
        $qb->where($qb->expr()->eq('p.products_id', $this->lastViewedProductId));
        
        $this->prepareDefaultStatement($qb, $this->settings, $this->configurationFinder, $this->connection);
        
        return $qb->executeQuery();
    }
}