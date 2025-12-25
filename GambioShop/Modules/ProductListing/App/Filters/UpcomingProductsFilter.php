<?php
/*
 * --------------------------------------------------------------
 *   UpcomingProductsFilter.php 2023-06-14
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
use Gambio\Shop\Modules\ProductListing\App\Event\UpcomingProductsCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;

/**
 * Class UpcomingProductsFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class UpcomingProductsFilter implements ListingFilter
{
    use FilterUtilityTrait;
    
    private ListingSettings     $settings;
    private Connection          $connection;
    private ConfigurationFinder $configurationFinder;
    
    
    public function __construct(
        ListingSettings     $settings,
        Connection          $connection,
        ConfigurationFinder $configurationFinder
    ) {
        $this->settings            = $settings;
        $this->connection          = $connection;
        $this->configurationFinder = $configurationFinder;
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
        return new ListingSortOrder(ListingSortValue::dateAvailable(), ListingSortDirection::asc());
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
        return new UpcomingProductsCollected($listing);
    }
    
    
    /**
     * Builds a sql statement fetching ids for products that should appear as upcoming.
     * This method also takes group and fsk checks into account.
     *
     * @return Result
     * @see \UpcomingProductsMainThemeContentView::build_sql_query()
     *
     */
    private function buildStatement(): Result
    {
        $qb = $this->connection->createQueryBuilder();
        
        $qb->select('p.products_id')
            ->from('products', 'p')
            ->from('products_description', 'pd')
            ->where($qb->expr()
                        ->gt('p.products_date_available',
                             'NOW()'))
            ->andWhere($qb->expr()->eq('p.products_id', 'pd.products_id'))
            ->andWhere($qb->expr()->eq('pd.language_id', $this->getFilterLanguageId()))
            ->andWhere($qb->expr()->eq('p.products_startpage', '1'));
        
        $this->prepareDefaultStatement($qb, $this->settings, $this->configurationFinder, $this->connection);
        
        return $qb->executeQuery();
    }
    
    
    /**
     * @return int
     */
    private function getFilterLanguageId(): int
    {
        return $this->settings->languageId();
    }
}