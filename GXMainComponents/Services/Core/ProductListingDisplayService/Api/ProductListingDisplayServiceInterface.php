<?php
/* --------------------------------------------------------------
  ProductListingDisplayServiceInterface.php 2023-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Interface ProductListingDisplayServiceInterface
 *
 * An adapter service that is supposed to work as a mediator between
 * frontend and backend for Gambio\Shop\Modules\ProductListing.
 *
 * Example Usage:
 * StaticGXCoreLoader::getService('ProductListingDisplayService')
 *
 */
interface ProductListingDisplayServiceInterface
{
    /**
     * Gets a list of purchased products similar to give product.
     *
     * @param array $original
     * @param int   $productId
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\AlsoPurchasedFilter
     */
    public function getAlsoPurchased(array $original, int $productId): array;


    /**
     * Gets a list of last viewed products similar to give product.
     *
     * @param array $original
     * @param int   $productId
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\LastViewedFilter
     */
    public function getLastViewed(array $original, int $productId): array;


    /**
     * Gets a list of newly published products.
     * Freshness is controlled by Configurations > Startpage > Maximum days for new products
     *
     * SELECT * FROM `gx_configurations` WHERE `key` LIKE 'configuration/MAX_DISPLAY_NEW_PRODUCTS_DAYS'
     *
     * @param array $original
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\NewProductsFilter
     */
    public function getNewProducts(array $original): array;


    /**
     * @param array $original
     *
     * Gets a list of 'Specials' products sorted randomly. Customer groups & FSK18 check are considered in the query.
     * Count is controlled by Configurations > Startpage > Selection of Products on Special
     *
     * SELECT * FROM `gx_configurations` WHERE `key` LIKE 'configuration/MAX_RANDOM_SELECT_SPECIALS'
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\SpecialsBoxFilter
     */
    public function getSpecialsBox(array $original): array;


    /**
     * Gets a list of 'Specials' products.
     *
     * @param array $original
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\SpecialsFilter
     */
    public function getSpecials(array $original): array;


    /**
     * Gets a list of startpage products that are also marked as 'Specials'.
     *
     * @param array $original
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\StartpageSpecialsFilter
     */
    public function getStartpageSpecials(array $original): array;


    /**
     * Gets a list of startpage products. Customer groups & FSK18 check are considered in the query.
     *
     * @param array $original
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\TopProductsFilter
     */
    public function getTopProducts(array $original): array;


    /**
     * Gets a list of products with 'products_date_available' is after NOW().
     *
     * @param array $original
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\UpcomingProductsFilter
     */
    public function getUpcomingProducts(array $original): array;


    /**
     * Gets a list of "What is new" box products.
     *
     * @param array    $original
     * @param int|null $excludedProductId
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\WhatIsNewBoxFilter
     */
    public function getWhatIsNewBoxProducts(array $original, ?int $excludedProductId = null): array;
    
    
    /**
     * Gets a list of cross-selling products.
     *
     * @param array $original
     * @param int   $baseProductId
     * @param bool  $isReversed
     *
     * @return array
     * @see Gambio\Shop\Modules\ProductListing\App\Filters\CrossSellingFilter
     */
    public function getCrossSellingProducts(array $original, int $baseProductId, bool $isReversed): array;
}