<?php
/* --------------------------------------------------------------
   FeaturedProductReadService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FeaturedProductReadService implements FeaturedProductReadServiceInterface
{
    /**
     * @var FeaturedProductRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * FeaturedProductReadService constructor.
     *
     * @param FeaturedProductRepositoryInterface $repository
     */
    public function __construct(FeaturedProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Get Offers by given product id.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getOfferedProducts(FeaturedProductSettings $settings)
    {
        return $this->repository->getOfferedProducts($settings);
    }
    
    
    /**
     * get top products by given product id.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getTopProducts(FeaturedProductSettings $settings)
    {
        return $this->repository->getTopProducts($settings);
    }
    
    
    /**
     * get upcoming products by given id and date.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getUpcomingProducts(FeaturedProductSettings $settings)
    {
        return $this->repository->getUpcomingProducts($settings);
    }
    
    
    /**
     * get new products by given id.
     *
     * @param FeaturedProductSettings $settings
     *
     * @return FeaturedProductCollection
     */
    public function getNewProducts(FeaturedProductSettings $settings)
    {
        return $this->repository->getNewProducts($settings);
    }
    
    
    /**
     * get products by category id.
     *
     * @param FeaturedProductSettings $settings
     *
     * @param IntType                 $categoryId
     *
     * @return FeaturedProductCollection
     */
    public function getProductsByCategoryId(FeaturedProductSettings $settings, IntType $categoryId)
    {
        return $this->repository->getProductsByCategoryId($settings, $categoryId);
    }
}