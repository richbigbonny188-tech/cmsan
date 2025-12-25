<?php
/* --------------------------------------------------------------
  FiltersFactory.inc.php 2023-06-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Doctrine\DBAL\Connection;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingModelFactory;
use Gambio\Shop\Modules\ProductListing\App\Filters\AlsoPurchasedFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\CrossSellingFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\LastViewedFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\NewProductsFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\ReverseCrossSellingFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\SpecialsBoxFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\SpecialsFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\StartpageSpecialsFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\TopProductsFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\UpcomingProductsFilter;
use Gambio\Shop\Modules\ProductListing\App\Filters\WhatIsNewBoxFilter;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;
use InvalidArgumentException;

/**
 * Class FiltersFactory
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class FiltersFactory
{
    /**
     * @param Connection                 $connection
     * @param ConfigurationFinder        $configurationFinder
     * @param ProductListingModelFactory $modelsFactory
     */
    public function __construct(
        private Connection                 $connection,
        private ConfigurationFinder        $configurationFinder,
        private ProductListingModelFactory $modelsFactory
    )
    {
    }


    /**
     * @param int             $productId
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createAlsoPurchased(int $productId, ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            AlsoPurchasedFilter::class,
            $productId,
            $settings,
            $this->connection,
            $this->configurationFinder,
            $this->modelsFactory
        );
    }


    /**
     * @param string $className
     * @param        ...$args
     *
     * @return ListingFilter
     */
    private function createFilter(string $className, ...$args): ListingFilter
    {
        if (false === class_exists($className)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Filter "%s" was not found',
                    $className
                )
            );
        }

        if (false === is_subclass_of($className, ListingFilter::class)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Filter class "%s" provided has to implement "%s".',
                    $className,
                    ListingFilter::class
                )
            );
        }

        return new $className(...$args);
    }


    /**
     * @param int             $productId
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createLastViewed(int $productId, ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            LastViewedFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder,
            $productId
        );
    }


    /**
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createNewProducts(ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            NewProductsFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder
        );
    }


    /**
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createSpecialsBox(ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            SpecialsBoxFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder,
            $this->modelsFactory
        );
    }


    /**
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createSpecials(ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            SpecialsFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder
        );
    }


    /**
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createStartpageSpecials(ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            StartpageSpecialsFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder
        );
    }


    /**
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createTopProducts(ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            TopProductsFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder
        );
    }


    /**
     * @param ListingSettings $settings
     *
     * @return ListingFilter
     */
    public function createUpcomingProducts(ListingSettings $settings): ListingFilter
    {
        return $this->createFilter(
            UpcomingProductsFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder
        );
    }


    /**
     * @param ListingSettings $settings
     * @param int|null        $excludedProductId
     *
     * @return WhatIsNewBoxFilter
     */
    public function createWhatIsNewBoxProducts(
        ListingSettings $settings,
        ?int            $excludedProductId = null
    ): ListingFilter
    {

        return $this->createFilter(
            WhatIsNewBoxFilter::class,
            $settings,
            $this->connection,
            $this->configurationFinder,
            $this->modelsFactory,
            $excludedProductId
        );
    }
    
    
    /**
     * @param ListingSettings $settings
     * @param int             $baseProductId
     * @param bool            $isReversed
     *
     * @return CrossSellingFilter
     */
    public function createCrossSellingProducts(
        ListingSettings $settings,
        int             $baseProductId,
        bool            $isReversed
    ): ListingFilter {
        $filterClass = $isReversed ? ReverseCrossSellingFilter::class : CrossSellingFilter::class;
        
        return $this->createFilter($filterClass,
                                   $baseProductId,
                                   $settings,
                                   $this->connection,
                                   $this->configurationFinder,
                                   $this->modelsFactory,);
    }
}