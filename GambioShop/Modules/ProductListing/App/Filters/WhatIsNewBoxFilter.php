<?php
/*--------------------------------------------------------------
   WhatIsNewBoxFilter.php 2023-06-14
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
use Gambio\Shop\Modules\ProductListing\App\Event\WhatIsNewListingCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;

/**
 * Class WhatIsNewBoxFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class WhatIsNewBoxFilter implements ListingFilter
{
    use FilterUtilityTrait;
    
    /**
     * @param ListingSettings            $settings
     * @param Connection                 $connection
     * @param ConfigurationFinder        $configurationFinder
     * @param ProductListingModelFactory $factory
     * @param int|null                   $excludedProductId used if WhatISNewBox is displayed on a product page to
     *                                                      filter currently viewed product
     */
    public function __construct(
        private ListingSettings            $settings,
        private Connection                 $connection,
        private ConfigurationFinder        $configurationFinder,
        private ProductListingModelFactory $factory,
        private ?int                       $excludedProductId = null
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
    public function getListingEvent(Listing $listing): ?object
    {
        return new WhatIsNewListingCollected($listing);
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function createQuery(): QueryBuilder
    {
        $maxDisplayNewProducts = $this->getMaxDisplayNewProductsDays();
        $query                 = $this->connection->createQueryBuilder()
            ->select('products_id')
            ->from('products', 'p')
            ->setMaxResults($this->getMaxRandomSelectNew())
            ->where('products_id != :product_id')
            ->setParameter('product_id', $this->excludedProductId ?? 0);
        
        if ($maxDisplayNewProducts !== 0) {
            
            $date = date('Y.m.d', mktime(1, 1, 1, (int)date('m'), date('d') - $maxDisplayNewProducts, (int)date('Y')));
            
            $query->andWhere('products_date_added > :date_new_products')->setParameter('date_new_products', $date);
        }
        
        if ($this->isGroupCheckEnabled($this->configurationFinder)) {
            
            $customerGroupId = $this->customerIdToStatusId($this->settings->customerId(),
                                                           $this->connection,
                                                           $this->configurationFinder);
            $query           = $this->andWhereCustomerGroupCheck($query, $customerGroupId);
        }
        
        if ($this->isFskHidden($this->settings, $this->configurationFinder, $this->connection)) {
            
            $query = $this->andWhereHiddenFsk18($query);
        }
        
        $query->orderBy('products_id', 'DESC');
        
        return $query;
    }
    
    
    /**
     * @return int
     */
    public function getMaxDisplayNewProductsDays(): int
    {
        return (int)$this->configurationFinder->get('configuration/MAX_DISPLAY_NEW_PRODUCTS_DAYS');
    }
    
    
    /**
     * @return int
     */
    public function getMaxRandomSelectNew(): int
    {
        return (int)$this->configurationFinder->get('configuration/MAX_RANDOM_SELECT_NEW');
    }
}