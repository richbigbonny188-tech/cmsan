<?php
/* --------------------------------------------------------------
   ProductPriceRepository.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceRepository
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Repositories
 */
class ProductPriceRepository implements ProductPriceRepositoryInterface
{
    /**
     * @var \ProductPriceMapperInterface
     */
    protected $mapper;
    
    
    /**
     * ProductPriceRepository constructor.
     *
     * @param \ProductPriceMapperInterface $mapper
     */
    public function __construct(ProductPriceMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns the product price by given product id.
     *
     * @param \IdType $productId
     *
     * @return \ProductPriceInterface|null
     */
    public function findById(IdType $productId)
    {
        return $this->mapper->findById($productId);
    }
    
    
    /**
     * Returns the product price that matches the given search condition.
     *
     * @param \IdType      $productId
     * @param \IdType      $customerGroupId
     * @param \DecimalType $quantity
     *
     * @return \GraduatedPriceInterface
     */
    public function findByQuantity(IdType $productId, IdType $customerGroupId, DecimalType $quantity)
    {
        return $this->mapper->findByQuantity($productId, $customerGroupId, $quantity);
    }
    
    
    /**
     * Saves or updates the given product price in the storage.
     *
     * @param \ProductPriceInterface $productPrice Product price to be saved or updated.
     * @param \IdType                $productId    Id of product to be updated.
     *
     * @return ProductPriceInterface
     */
    public function save(ProductPriceInterface $productPrice, IdType $productId)
    {
        $price = $this->mapper->save($productPrice, $productId);
        
        return $price;
    }
    
    
    /**
     * Deletes the given product price from the storage.
     *
     * @param \ProductPriceInterface $productPrice Product price to be deleted.
     *
     * @return ProductPriceInterface
     */
    public function delete(ProductPriceInterface $productPrice)
    {
        return $this->mapper->delete($productPrice);
    }
}