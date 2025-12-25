<?php
/* --------------------------------------------------------------
   GraduatedPriceSerializer.inc.php 2018-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GraduatedPriceSerializer
{
    /**
     * Serialize a value to a JSON string.
     *
     * @param \GraduatedPriceInterface $graduatedPrice Content to be serialized.
     *
     * @return array
     */
    public function serialize(GraduatedPriceInterface $graduatedPrice)
    {
        $data = [
            'graduatedPrice' => $graduatedPrice->graduatedPrice(),
            'taxClassId'     => $graduatedPrice->taxClassId()
        ];
        
        return $data;
    }
    
    
    /**
     * Serializes the graduated price collection.
     *
     * @param \GraduatedPriceCollection $collection
     *
     * @return array
     */
    public function serializeCollection(GraduatedPriceCollection $collection)
    {
        $data = [];
        
        /** @var \GraduatedPriceInterface $graduatedPrice */
        foreach ($collection as $graduatedPrice) {
            $data[] = [
                'graduatedPrice' => $graduatedPrice->graduatedPrice(),
                'threshold'      => $graduatedPrice->threshold()
            ];
        }
        
        return $data;
    }
    
    
    /**
     * Deserialize the given json string to a product price quantity entity.
     *
     * @param mixed $graduatedPrice
     *
     * @return \GraduatedPrice
     */
    public function deserialize($graduatedPrice)
    {
        $graduatedPriceData = is_array($graduatedPrice) ? $graduatedPrice : json_decode($graduatedPrice, true);
        
        if ($graduatedPriceData === false || $graduatedPriceData === null) {
            throw new InvalidArgumentException('Invalid argument provided for graduated price deserialization.');
        }
        
        $graduatedPrice = $graduatedPriceData['graduatedPrice'];
        $threshold      = $graduatedPriceData['threshold'];
        
        return GraduatedPrice::createWithThreshold($graduatedPrice, $threshold);
    }
    
    
    /**
     * @param mixed $graduatedPriceCollectionData
     *
     * @return \GraduatedPriceCollection
     */
    public function deserializeCollection($graduatedPriceCollectionData)
    {
        $graduatedPriceArray = is_array($graduatedPriceCollectionData) ? $graduatedPriceCollectionData : json_decode($graduatedPriceCollectionData,
                                                                                                                     true);
        
        if ($graduatedPriceArray === false || $graduatedPriceArray === null) {
            throw new InvalidArgumentException('Invalid argument provided for graduated price deserialization.');
        }
        
        $graduatedPriceCollection = [];
        
        foreach ($graduatedPriceArray as $graduatedPriceData) {
            $graduatedPriceCollection[] = $this->deserialize($graduatedPriceData);
        }
        
        return GraduatedPriceCollection::collect($graduatedPriceCollection);
    }
}