<?php
/* --------------------------------------------------------------
   CustomerGroupSerializerTest.php 2018-03-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupCollectionSerializer
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
class CustomerGroupCollectionSerializer implements CustomerGroupCollectionSerializerInterface
{
    /**
     * @var \CustomerGroupSerializerInterface
     */
    protected $customerGroupSerializer;
    
    /**
     * @var \CustomerGroupFactory
     */
    protected $factory;
    
    
    public function __construct(
        CustomerGroupSerializerInterface $customerGroupSerializer,
        CustomerGroupFactory $factory
    ) {
        $this->customerGroupSerializer = $customerGroupSerializer;
        $this->factory                 = $factory;
    }
    
    
    /**
     * Serialize a value to a JSON string.
     *
     * @param \CustomerGroupCollection $collection Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupCollection $collection)
    {
        $data = [];
        foreach ($collection->getArray() as $customerGroup) {
            $data[] = $this->customerGroupSerializer->serialize($customerGroup);
        }
        
        return $data;
    }
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupCollection $collection Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupCollection $collection)
    {
        return json_encode($this->serialize($collection), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param \StringType $collection Content to be deserialize.
     *
     * @return \CustomerGroupCollection
     */
    public function deserialize(StringType $collection)
    {
        $customerGroupData       = json_decode($collection->asString(), true);
        $customerGroupCollection = $this->factory->createCollection();
        
        foreach ($customerGroupData as $data) {
            $customerGroupCollection->addItem($this->customerGroupSerializer->deserialize(new StringType(json_encode($data))));
        }
        
        return $customerGroupCollection;
    }
}