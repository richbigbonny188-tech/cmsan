<?php
/* --------------------------------------------------------------
   ProductPriceSerializer.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceSerializer
 */
class ProductPriceSerializer
{
    /**
     * @var ProductGroupPriceSerializer
     */
    protected $groupPriceSerializer;
    
    
    /**
     * ProductPriceSerializer constructor.
     *
     * @param \ProductGroupPriceSerializer $groupPriceSerializer
     */
    public function __construct(ProductGroupPriceSerializer $groupPriceSerializer)
    {
        $this->groupPriceSerializer = $groupPriceSerializer;
    }
    
    
    /**
     * Serializes the given product price into an array.
     *
     * @param \ProductPriceInterface $productPrice Product price to be serialized.
     *
     * @return array Serialized product price.
     */
    public function serialize(ProductPriceInterface $productPrice)
    {
        $data = [
            'productId'  => $productPrice->productId(),
            'price'      => $productPrice->price(),
            'taxClassId' => $productPrice->taxClassId()
        ];
        
        if ($productPrice->groupPrices()) {
            $data['groupPrices'] = $this->groupPriceSerializer->serializeCollection($productPrice->groupPrices());
        }
        
        return $data;
    }
    
    
    /**
     * Deserialize the given json string to a product price entity.
     *
     * @param string|array $productPrice Product price.
     *
     * @return \ProductPrice Product price entity.
     */
    public function deserialize($productPrice)
    {
        $productPriceData = is_array($productPrice) ? $productPrice : json_decode($productPrice, true);
        
        if (empty($productPriceData)) {
            throw new InvalidArgumentException('Invalid argument provided for product price deserialization.');
        }
        
        if (!array_key_exists('groupPrices', $productPriceData)) {
            $productPriceData['groupPrices'] = [];
        }
        
        $productId            = $productPriceData['productId'];
        $price                = $productPriceData['price'];
        $groupPriceCollection = $this->groupPriceSerializer->deserializeCollection($productPriceData['groupPrices']);
        
        return ProductPrice::createWithoutTaxClassId($productId, $price, $groupPriceCollection);
    }
}