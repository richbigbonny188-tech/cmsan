<?php
/* --------------------------------------------------------------
   ParcelServiceCollectionSerializer.inc.php 2018-07-25 
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceCollectionSerializer
 */
class ParcelServiceCollectionSerializer
{
    /**
     * @var \ParcelServiceSerializer
     */
    protected $parcelServiceSerializer;
    
    
    /**
     * ParcelServiceCollectionSerializer constructor.
     *
     * @param \ParcelServiceSerializer $parcelServiceSerializer
     */
    public function __construct(ParcelServiceSerializer $parcelServiceSerializer)
    {
        $this->parcelServiceSerializer = $parcelServiceSerializer;
    }
    
    
    /**
     * Serializes the given parcel services into an array.
     *
     * @param \ParcelServiceCollection $parcelServices Parcel service entities to be serialized.
     *
     * @return array Serialized parcel services.
     */
    public function serialize(ParcelServiceCollection $parcelServices)
    {
        $data = [];
        
        foreach ($parcelServices as $parcelService) {
            $data[] = $this->parcelServiceSerializer->serialize($parcelService);
        }
        
        return $data;
    }
    
    
    /**
     * Serializes the given parcel services into a json string.s
     *
     * @param \ParcelServiceCollection $parcelServices Parcel service entities to be serialized.
     *
     * @return string Encoded parcel services.
     */
    public function encode(ParcelServiceCollection $parcelServices)
    {
        return json_encode($this->serialize($parcelServices));
    }
    
    
    /**
     * Deserialize the given parcel services data into a parcel service collection.
     *
     * @param string|array $parcelServicesData Parcel services data. If string, it must be a valid json.
     *
     * @return \ParcelServiceCollection Deserialized parcel service data.
     */
    public function deserialize($parcelServicesData)
    {
        $data = is_string($parcelServicesData) ? json_decode($parcelServicesData, true) : $parcelServicesData;
        
        if (!$data) {
            throw new InvalidArgumentException('Parcel services json string is invalid and can not be deserialized!');
        }
        
        $parcelServices = [];
        foreach ($parcelServicesData as $parcelServiceData) {
            $parcelServiceData[] = $this->parcelServiceSerializer->deserialize($parcelServiceData);
        }
        
        return ParcelServiceCollection::collect($parcelServices);
    }
}