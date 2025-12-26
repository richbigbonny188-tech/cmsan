<?php
/*--------------------------------------------------------------
   AlsoPurchasedFilter.php 2024-01-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Filters;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingModelFactory;
use Gambio\Shop\Modules\ProductListing\App\Event\AlsoPurchasedCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;

/**
 * Class AlsoPurchasedFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class AlsoPurchasedFilter implements ListingFilter
{
    use FilterUtilityTrait;
    
    /**
     * AlsoPurchasedFilter constructor.
     *
     * @param int                        $baseProductId
     * @param ListingSettings            $settings
     * @param Connection                 $connection
     * @param ConfigurationFinder        $configurationFinder
     * @param ProductListingModelFactory $factory
     */
    public function __construct(
        private int                        $baseProductId,
        private ListingSettings            $settings,
        private Connection                 $connection,
        private ConfigurationFinder        $configurationFinder,
        private ProductListingModelFactory $factory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductIds(): ListingItemIds
    {
        $productListingIds = $this->createQuery()->executeQuery()->fetchAllAssociative();
        $productListingIds = array_column($productListingIds, 'products_id');
        $productListingIds = array_map('intval', $productListingIds);
        $productListingIds = array_map([$this->factory, 'createListingItemId'], $productListingIds);
        
        return new ListingItemIds(...$productListingIds);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSortOrder(): ListingSortOrder
    {
        return new ListingSortOrder(ListingSortValue::id(), ListingSortDirection::desc());
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
    public function getListingEvent(Listing $listing): AlsoPurchasedCollected
    {
        return new AlsoPurchasedCollected($listing);
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function createQuery(): QueryBuilder
    {
        $orderIdQuery = $this->connection->createQueryBuilder()
            ->select('o.orders_id')
            ->from('orders', 'o')
            ->innerJoin('o', 'orders_products', 'op', 'o.orders_id=op.orders_id')
            ->where('op.products_id=:base_product_id')
            ->andWhere('o.date_purchased > (NOW() - INTERVAL :max_also_purchased_days DAY)');
            
        
        $query = $this->connection->createQueryBuilder()
            ->select('p.products_id')
            ->from('products', 'p')
            ->innerJoin('p', 'orders_products', 'op', 'p.products_id=op.products_id')
            ->where('op.orders_id IN (' . $orderIdQuery->getSQL() . ')')
            ->andWhere('op.products_id != :base_product_id')
            ->andWhere('p.products_status = 1')
            ->groupBy('p.products_id')
            ->setMaxResults($this->maxDisplayAlsoPurchased());
        
        $query->setParameter('base_product_id', $this->baseProductId);
        $query->setParameter('max_also_purchased_days', $this->maxDisplayAlsoPurchasedDays());
        
        if ($this->isGroupCheckEnabled($this->configurationFinder)) {
            $customerGroupId = $this->customerIdToStatusId($this->settings->customerId(),
                                                           $this->connection,
                                                           $this->configurationFinder);
            $query           = $this->andWhereCustomerGroupCheck($query, $customerGroupId);
        }
        
        if ($this->isFskHidden($this->settings, $this->configurationFinder, $this->connection)) {
            $query = $this->andWhereHiddenFsk18($query);
        }
        
        return $query;
    }
    
    
    /**
     * @return int
     */
    private function maxDisplayAlsoPurchasedDays(): int
    {
        return (int)$this->configurationFinder->get('configuration/MAX_DISPLAY_ALSO_PURCHASED_DAYS');
    }
    
    
    /**
     * @return int
     */
    private function maxDisplayAlsoPurchased(): int
    {
        return (int)$this->configurationFinder->get('configuration/MAX_DISPLAY_ALSO_PURCHASED');
    }
}