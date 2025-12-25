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
 * Class CustomerGroupSerializer
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
class CustomerGroupSerializer implements CustomerGroupSerializerInterface
{
    /**
     * @var \CustomerGroupSettingsSerializerInterface
     */
    protected $settingsSerializer;
    
    /**
     * @var \CustomerGroupConfigurationsSerializerInterface
     */
    protected $configurationsSerializer;
    
    /**
     * @var \CustomerGroupFactory
     */
    protected $factory;
    
    
    /**
     * CustomerGroupSerializer constructor.
     *
     * @param \CustomerGroupSettingsSerializerInterface       $settingsSerializer
     * @param \CustomerGroupConfigurationsSerializerInterface $configurationsSerializer
     * @param \CustomerGroupFactory                           $customerGroupFactory
     */
    public function __construct(
        CustomerGroupSettingsSerializerInterface $settingsSerializer,
        CustomerGroupConfigurationsSerializerInterface $configurationsSerializer,
        CustomerGroupFactory $customerGroupFactory
    ) {
        $this->settingsSerializer       = $settingsSerializer;
        $this->configurationsSerializer = $configurationsSerializer;
        $this->factory                  = $customerGroupFactory;
    }
    
    
    /**
     * Serialize a value to a JSON string.
     *
     * @param \CustomerGroupInterface $customerGroup Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupInterface $customerGroup)
    {
        return [
            'id'             => $customerGroup->getId(),
            'names'          => $this->_serializeCustomerGroupNames($customerGroup->getNames()),
            'settings'       => $this->settingsSerializer->serialize($customerGroup->getSettings()),
            'configurations' => $this->configurationsSerializer->serialize($customerGroup->getConfigurations())
        ];
    }
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupInterface $customerGroup Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupInterface $customerGroup)
    {
        return json_encode($this->serialize($customerGroup), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param \StringType $customerGroup Content to be deserialize.
     *
     * @return \CustomerGroupInterface
     */
    public function deserialize(StringType $customerGroup)
    {
        $customerGroupData   = json_decode($customerGroup->asString(), true);
        $customerGroupObject = $this->factory->createEntity()->setId(new IntType($customerGroupData['id']));
        
        foreach ($customerGroupData['names'] as $languageCode => $name) {
            $customerGroupObject->setName(new StringType($name), new LanguageCode(new StringType($languageCode)));
        }
        
        $customerGroupObject->setSettings($this->settingsSerializer->deserialize(new StringType(json_encode($customerGroupData['settings']))))
            ->setConfigurations($this->configurationsSerializer->deserialize(new StringType(json_encode($customerGroupData['configurations']))));
        
        return $customerGroupObject;
    }
    
    
    /**
     * Serializes customer group names array.
     *
     * @param array $customerGroupNames Customer group names array to be serialized.
     *
     * @return array Serialized customer group names array.
     */
    protected function _serializeCustomerGroupNames(array $customerGroupNames)
    {
        $data = [];
        foreach ($customerGroupNames as $languageCode => $name) {
            $data[$languageCode] = $name;
        }
        
        return $data;
    }
}