<?php
/*--------------------------------------------------------------
   CrossSellingFilter.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Filters;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingModelFactory;
use Gambio\Shop\Modules\ProductListing\App\Event\CrossSellingCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;

/**
 * Class CrossSellingFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class CrossSellingFilter implements ListingFilter
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
        protected int                        $baseProductId,
        protected ListingSettings            $settings,
        protected Connection                 $connection,
        protected ConfigurationFinder        $configurationFinder,
        protected ProductListingModelFactory $factory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductIds(): ListingItemIds
    {
        $groupId = $this->getBaseProductsCrossSellingGroupId();
        
        if ($groupId !== null) {
            
            $productListingIds = $this->createQuery($groupId)->executeQuery()->fetchAllAssociative();
            $productListingIds = array_column($productListingIds, 'products_id');
            $productListingIds = array_map('intval', $productListingIds);
            $productListingIds = array_map([$this->factory, 'createListingItemId'], $productListingIds);
    
            return new ListingItemIds(...$productListingIds);
        }
    
        return new ListingItemIds;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSortOrder(): ListingSortOrder
    {
        return new ListingSortOrder(ListingSortValue::crossSell(), ListingSortDirection::asc());
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
        return new CrossSellingCollected($listing);
    }
    
    /**
     * @param int $xSellGroupId
     *
     * @return QueryBuilder
     */
    protected function createQuery(int $xSellGroupId): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder()
            ->select('p.products_id')
            ->from('products', 'p')
            ->from('products_xsell', 'xp')
            ->where('xp.products_id = :base_product_id')
            ->setParameter('base_product_id', $this->baseProductId)
            ->andWhere('xp.products_xsell_grp_name_id = :xsell_group_id')
            ->setParameter('xsell_group_id', $xSellGroupId)
            ->andWhere('p.products_status = :status')
            ->setParameter('status', 1)
            ->andWhere('xp.xsell_id = p.products_id')
            ->orderBy('xp.sort_order', 'ASC');
        
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
     * @return null|int
     */
    protected function getBaseProductsCrossSellingGroupId(): ?int
    {
        $groupIdResult = $this->connection->createQueryBuilder()
            ->select('products_xsell_grp_name_id')
            ->from('products_xsell')
            ->where('products_id = :base_product_id')
            ->setParameter('base_product_id', $this->baseProductId)
            ->groupBy('products_xsell_grp_name_id')
            ->executeQuery();
        
        if ($groupIdResult->rowCount() === 0) {
            
            return null;
        }
        
        return (int)$groupIdResult->fetchAssociative()['products_xsell_grp_name_id'];
    }
}