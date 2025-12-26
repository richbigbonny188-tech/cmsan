<?php
/* --------------------------------------------------------------
   FeaturedProductReadServiceInterface.inc.php 2019-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

interface FeaturedProductReadServiceInterface
{
    /**
     * Get Offers by given product id.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getOfferedProducts(FeaturedProductSettings $settings);
    
    
    /**
     * get top products by given product id.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getTopProducts(FeaturedProductSettings $settings);
    
    
    /**
     * get upcoming products by given id and date.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getUpcomingProducts(FeaturedProductSettings $settings);
    
    
    /**
     * get new products by given id.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getNewProducts(FeaturedProductSettings $settings);
}