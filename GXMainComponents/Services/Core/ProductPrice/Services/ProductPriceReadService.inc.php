<?php

/* --------------------------------------------------------------
   ProductPriceReadService.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceReadService
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Services
 */
class ProductPriceReadService implements ProductPriceReadServiceInterface
{
    /**
     * @var \ProductPriceRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ProductPriceReadService constructor.
     *
     * @param \ProductPriceRepositoryInterface $repository
     */
    public function __construct(ProductPriceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns product price by given id.
     *
     * @param \IdType $productId Product id.
     *
     * @return \ProductPriceInterface
     * @throws \ProductPriceNotFoundException
     */
    public function getById(IdType $productId)
    {
        $price = $this->repository->findById($productId);
        if (!$price) {
            throw new ProductPriceNotFoundException('Product price by given product id "' . $productId->asInt()
                                                    . '" not found!');
        }
        
        return $price;
    }
    
    
    /**
     * Returns the product quantity price.
     *
     * @param \IdType      $productId
     * @param \IdType      $customerGroupId
     * @param \DecimalType $quantity
     *
     * @return \GraduatedPriceInterface
     * @throws \ProductQuantityPriceNotFoundException
     */
    public function getByQuantity(IdType $productId, IdType $customerGroupId, DecimalType $quantity)
    {
        $quantityPrice = $this->repository->findByQuantity($productId, $customerGroupId, $quantity);
        
        if (!$quantityPrice) {
            throw new ProductQuantityPriceNotFoundException('Product quantity price not found by given product id "'
                                                            . $productId->asInt() . '", customer group id "'
                                                            . $customerGroupId->asInt() . '" and quantity "'
                                                            . $quantity->asDecimal() . '"!');
        }
        
        return $quantityPrice;
    }
}