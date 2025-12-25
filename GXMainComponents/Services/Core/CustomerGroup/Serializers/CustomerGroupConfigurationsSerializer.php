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
 * Class CustomerGroupConfigurationsSerializer
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
class CustomerGroupConfigurationsSerializer implements CustomerGroupConfigurationsSerializerInterface
{
    /**
     * @var \CustomerGroupFactory
     */
    protected $factory;
    
    
    /**
     * CustomerGroupConfigurationsSerializer constructor.
     *
     * @param \CustomerGroupFactory $customerGroupFactory
     */
    public function __construct(CustomerGroupFactory $customerGroupFactory)
    {
        $this->factory = $customerGroupFactory;
    }
    
    
    /**
     * Serialize a value to a JSON string.
     *
     * @param \CustomerGroupConfigurationsInterface $configurations Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupConfigurationsInterface $configurations)
    {
        return [
            'minOrder'                 => $configurations->getMinOrder(),
            'maxOrder'                 => $configurations->getMaxOrder(),
            'discount'                 => $configurations->getDiscount(),
            'otDiscount'               => $configurations->getOtDiscount(),
            'unallowedPaymentModules'  => $configurations->getUnallowedPaymentModules(),
            'unallowedShippingModules' => $configurations->getUnallowedShippingModules()
        ];
    }
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupConfigurationsInterface $configuration Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupConfigurationsInterface $configuration)
    {
        return json_encode($this->serialize($configuration), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param StringType $configurations Content to be deserialize.
     *
     * @return \CustomerGroupConfigurationsInterface
     */
    public function deserialize(StringType $configurations)
    {
        $configurations = json_decode($configurations->asString(), true);
        
        return $this->factory->createConfigurations(new DecimalType($configurations['discount']),
                                                    new DecimalType($configurations['otDiscount']),
                                                    $configurations['minOrder'] ? new DecimalType($configurations['minOrder']) : null,
                                                    $configurations['maxOrder'] ? new DecimalType($configurations['maxOrder']) : null,
                                                    $configurations['unallowedPaymentModules'],
                                                    $configurations['unallowedShippingModules']);
    }
}