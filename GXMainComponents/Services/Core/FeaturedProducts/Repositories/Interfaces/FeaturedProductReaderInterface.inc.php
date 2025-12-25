<?php
/* --------------------------------------------------------------
   FeaturedProductReaderInterface.inc.php 2019-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface FeaturedProductReaderInterface
 */
interface FeaturedProductReaderInterface
{
    /**
     * Get offers
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getOfferedProducts(FeaturedProductSettings $settings);
    
    
    /**
     * Get top products
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getTopProducts(FeaturedProductSettings $settings);
    
    
    /**
     * Get upcoming products
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getUpcomingProducts(FeaturedProductSettings $settings);
    
    
    /**
     * Get new products
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getNewProducts(FeaturedProductSettings $settings);
    
    
    /**
     * Get products by category
     *
     * @param FeaturedProductSettings $settings
     *
     * @param IntType                 $categoryId
     *
     * @return FeaturedProductCollection
     */
    public function getProductsByCategoryId(FeaturedProductSettings $settings, IntType $categoryId);
}