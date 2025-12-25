<?php
/* --------------------------------------------------------------
   ProductPriceMapper.inc.php 2018-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceMapper
 */
class ProductPriceMapper implements ProductPriceMapperInterface
{
    /**
     * @var \ProductPriceReaderInterface
     */
    protected $reader;
    
    /**
     * @var \ProductPriceWriterInterface
     */
    protected $writer;
    
    
    /**
     * ProductPriceMapper constructor.
     *
     * @param \ProductPriceAdapterInterface $dataAdapter
     */
    public function __construct(ProductPriceAdapterInterface $dataAdapter)
    {
        $this->reader = $dataAdapter->reader();
        $this->writer = $dataAdapter->writer();
    }
    
    
    /**
     * Returns the product price by given product id.
     *
     * @param \IdType $productId
     *
     * @return ProductPriceInterface|null
     */
    public function findById(IdType $productId)
    {
        $data = $this->reader->fetchById($productId);
        if (!$data) {
            return null;
        }
        
        $productGroupPrices = [];
        
        foreach ($data['customerGroups'] as $groupData) {
            $graduatedPrices = [];
            
            if (array_key_exists('graduatedPrices', $groupData)) {
                foreach ($groupData['graduatedPrices'] as $graduatedPriceData) {
                    $graduatedPrices[] = GraduatedPrice::createWithThreshold($graduatedPriceData['graduatedPrice'],
                                                                             $graduatedPriceData['threshold']);
                }
            }
            
            $customerGroupId      = $groupData['customerGroupId'];
            $groupPrice           = array_key_exists('groupPrice', $groupData) ? $groupData['groupPrice'] : null;
            $graduatedPrices      = count($graduatedPrices)
                                    > 0 ? GraduatedPriceCollection::collect($graduatedPrices) : null;
            $productGroupPrices[] = ProductGroupPrice::create($customerGroupId, $groupPrice, $graduatedPrices);
        }
        
        $productId            = $data['productId'];
        $price                = $data['price'];
        $taxClassId           = $data['taxClassId'];
        $groupPriceCollection = ProductGroupPriceCollection::collect($productGroupPrices);
        
        return ProductPrice::create($productId, $price, $taxClassId, $groupPriceCollection);
    }
    
    
    /**
     * Returns the product prices that matches the given search condition.
     *
     * @param \IdType      $productId       Product id.
     * @param \IdType      $customerGroupId Customer group id.
     * @param \DecimalType $quantity        Quantity.
     *
     * @return \GraduatedPriceInterface|null
     */
    public function findByQuantity(IdType $productId, IdType $customerGroupId, DecimalType $quantity)
    {
        $data = $this->reader->fetchByQuantity($productId, $customerGroupId, $quantity);
        
        if (!$data) {
            return null;
        }
        
        return GraduatedPrice::create($data['graduatedPrice'], $data['taxClassId']);
    }
    
    
    /**
     * Saves or updates the given product price in the storage.
     *
     * @param \ProductPriceInterface $productPrice Product price to be saved or updated.
     * @param \IdType                $productId    Id of product to be updated.
     *
     * @return ProductPriceInterface
     *
     * @throws \EntityNotFoundException If product does not exist
     */
    public function save(ProductPriceInterface $productPrice, IdType $productId)
    {
        $productPriceData = [
            'price' => $productPrice->price()
        ];
        
        if ($this->findById($productId) === null) {
            throw new EntityNotFoundException('Product with id "' . $productId->asInt() . '" does not exist.');
        }
        
        foreach ($productPrice->groupPrices() as $groupPrice) {
            /** @var \ProductGroupPriceInterface $groupPrice */
            $graduatedPrices = [];
            
            foreach ($groupPrice->graduatedPrices() as $graduatedPrice) {
                /** @var \GraduatedPriceInterface $graduatedPrice */
                $graduatedPrices[] = [
                    'graduatedPrice' => $graduatedPrice->graduatedPrice(),
                    'threshold'      => $graduatedPrice->threshold()
                ];
            }
            
            $productPriceData['customerGroups'][] = [
                'customerGroupId' => $groupPrice->customerGroupId(),
                'groupPrice'      => $groupPrice->groupPrice(),
                'graduatedPrices' => $graduatedPrices
            ];
        }
        $this->writer->update($productPriceData, $productId);
        
        return $this->findById($productId);
    }
    
    
    /**
     * Deletes the product price from the storage.uanti
     *
     * @param \ProductPriceInterface $productPrice Product price to be removed.
     *
     * @return void
     */
    public function delete(ProductPriceInterface $productPrice)
    {
        $this->writer->delete(new IdType($productPrice->productId()));
    }
}