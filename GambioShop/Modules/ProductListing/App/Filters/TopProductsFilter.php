<?php
/* --------------------------------------------------------------
   TopProductsFilter.php 2023-12-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Filters;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\App\Event\TopProductsCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;

/**
 * Class TopProductsFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class TopProductsFilter implements ListingFilter
{
    use FilterUtilityTrait;
    
    /**
     * TopProductsFilter constructor.
     *
     * @param Connection          $connection
     * @param ListingSettings     $settings
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(
        private ListingSettings     $settings,
        private Connection          $connection,
        private ConfigurationFinder $configurationFinder
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductIds(): ListingItemIds
    {
        $cb     = static function (array $element): ListingItemId {
            return new ListingItemId((int)$element['id']);
        };
        $result = $this->buildStatement();
        
        return new ListingItemIds(...array_map($cb, $result->fetchAllAssociative()));
    }
    
    
    /**
     * Builds a sql statement fetching ids for products that should appear on the start page.
     * This method also takes group and fsk checks into account.
     *
     * @return Result
     */
    private function buildStatement(): Result
    {
        $qb = $this->connection->createQueryBuilder();
        $qb = $qb->select('p.products_id as id')
            ->from('products', 'p')
            ->where($qb->expr()->eq('products_startpage', '1'))
            ->andWhere($qb->expr()->eq('products_status', '1'));
        
        if ($this->isFskHidden($this->settings, $this->configurationFinder, $this->connection)) {
            $qb = $this->andWhereHiddenFsk18($qb);
        }
        
        if ($this->isGroupCheckEnabled($this->configurationFinder)) {
            $customerGroupId = $this->customerIdToStatusId($this->settings->customerId(),
                                                           $this->connection,
                                                           $this->configurationFinder);
            $qb              = $this->andWhereCustomerGroupCheck($qb, $customerGroupId);
        }
        $qb = $qb->setMaxResults(50);
        return $qb->executeQuery();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSortOrder(): ListingSortOrder
    {
        return new ListingSortOrder(ListingSortValue::startPage(), ListingSortDirection::asc());
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
        return new TopProductsCollected($listing);
    }
}