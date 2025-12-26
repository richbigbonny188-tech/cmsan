<?php
/*--------------------------------------------------------------
   SpecialsBoxFilter.php 2023-06-14
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
use Gambio\Shop\Modules\ProductListing\App\Event\SpecialsBoxListingCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;

/**
 * Class SpecialsBoxFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class SpecialsBoxFilter implements ListingFilter
{
    use FilterUtilityTrait;
    
    private ListingSettings            $settings;
    private Connection                 $connection;
    private ConfigurationFinder        $configurationFinder;
    private ProductListingModelFactory $factory;
    
    
    /**
     * SpecialsBoxFilter constructor.
     *
     * @param ListingSettings            $settings
     * @param Connection                 $connection
     * @param ConfigurationFinder        $configurationFinder
     * @param ProductListingModelFactory $factory
     */
    public function __construct(
        ListingSettings            $settings,
        Connection                 $connection,
        ConfigurationFinder        $configurationFinder,
        ProductListingModelFactory $factory
    ) {
        $this->settings            = $settings;
        $this->connection          = $connection;
        $this->configurationFinder = $configurationFinder;
        $this->factory             = $factory;
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
        return new ListingSortOrder(ListingSortValue::dateAvailable(), ListingSortDirection::desc());
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
        return new SpecialsBoxListingCollected($listing);
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function createQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder()
            ->select('p.products_id')
            ->from('products', 'p')
            ->from('specials', 's')
            ->where('p.products_status=1')
            ->andWhere('s.status=1')
            ->andWhere('p.products_id=s.products_id')
            ->orderBy('s.specials_date_added', 'DESC')
            ->setMaxResults($this->getMaxRandomSelectSpecials());
        
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
    private function getMaxRandomSelectSpecials(): int
    {
        return (int)$this->configurationFinder->get('configuration/MAX_RANDOM_SELECT_SPECIALS');
    }
}