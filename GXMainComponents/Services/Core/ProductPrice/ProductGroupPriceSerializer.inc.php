<?php
/* --------------------------------------------------------------
   ProductGroupPriceSerializer.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ProductGroupPriceSerializer
{
    /**
     * @var GraduatedPriceSerializer
     */
    protected $graduatedPriceSerializer;
    
    
    /**
     * ProductGroupPriceSerializer constructor.
     *
     * @param \GraduatedPriceSerializer $graduatedPriceSerializer
     */
    public function __construct(GraduatedPriceSerializer $graduatedPriceSerializer)
    {
        $this->graduatedPriceSerializer = $graduatedPriceSerializer;
    }
    
    
    /**
     * Serializes the product group price.
     *
     * @param \ProductGroupPriceInterface $productGroupPrice
     *
     * @return array
     */
    public function serialize(ProductGroupPriceInterface $productGroupPrice)
    {
        $data = [
            'customerGroupId' => $productGroupPrice->customerGroupId()
        ];
        
        if ($productGroupPrice->groupPrice()) {
            $data['groupPrice'] = $productGroupPrice->groupPrice();
        }
        
        if ($productGroupPrice->graduatedPrices()) {
            $data['graduatedPrices'] = $this->graduatedPriceSerializer->serializeCollection($productGroupPrice->graduatedPrices());
        }
        
        return $data;
    }
    
    
    /**
     * Serializes the product group price collection.
     *
     * @param \ProductGroupPriceCollection $productGroupPriceCollection
     *
     * @return array
     */
    public function serializeCollection(ProductGroupPriceCollection $productGroupPriceCollection)
    {
        $data = [];
        
        /** @var \ProductGroupPriceInterface $productGroupPrice */
        foreach ($productGroupPriceCollection as $productGroupPrice) {
            $data[] = $this->serialize($productGroupPrice);
        }
        
        return $data;
    }
    
    
    /**
     * @param mixed $groupPriceDataArray
     *
     * @return \ProductGroupPrice
     */
    public function deserialize($groupPriceDataArray)
    {
        $groupPriceData = is_array($groupPriceDataArray) ? $groupPriceDataArray : json_decode($groupPriceDataArray,
                                                                                              true);
        
        if ($groupPriceData === false || $groupPriceData === null) {
            throw new InvalidArgumentException('Invalid argument provided for product group price deserialization.');
        }
        
        if (!array_key_exists('graduatedPrices', $groupPriceData)) {
            $groupPriceData['graduatedPrices'] = [];
        }
        
        if (!array_key_exists('groupPrice', $groupPriceData)) {
            $groupPriceData['groupPrice'] = 0.0;
        }
        
        $customerGroupId = $groupPriceData['customerGroupId'];
        $groupPrice      = $groupPriceData['groupPrice'];
        $graduatedPrices = $this->graduatedPriceSerializer->deserializeCollection($groupPriceData['graduatedPrices']);
        
        return ProductGroupPrice::create($customerGroupId, $groupPrice, $graduatedPrices);
    }
    
    
    /**
     * @param mixed $groupPriceCollectionData
     *
     * @return \ProductGroupPriceCollection
     */
    public function deserializeCollection($groupPriceCollectionData)
    {
        $groupPriceArray = is_array($groupPriceCollectionData) ? $groupPriceCollectionData : json_decode($groupPriceCollectionData,
                                                                                                         true);
        
        if ($groupPriceArray === false || $groupPriceArray === null) {
            throw new InvalidArgumentException('Invalid argument provided for product group price deserialization.');
        }
        
        $productGroupPrices = [];
        
        foreach ($groupPriceArray as $groupPriceData) {
            $productGroupPrices[] = $this->deserialize($groupPriceData);
        }
        
        return ProductGroupPriceCollection::collect($productGroupPrices);
    }
}