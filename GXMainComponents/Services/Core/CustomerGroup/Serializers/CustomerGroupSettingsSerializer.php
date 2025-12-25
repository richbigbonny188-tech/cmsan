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
 * Class CustomerGroupSettingsSerializer
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
class CustomerGroupSettingsSerializer implements CustomerGroupSettingsSerializerInterface
{
    /**
     * @var \CustomerGroupFactory
     */
    protected $factory;
    
    
    /**
     * CustomerGroupSettingsSerializer constructor.
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
     * @param \CustomerGroupSettingsInterface $settings Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupSettingsInterface $settings)
    {
        return [
            'public'             => $settings->isPublic(),
            'otDiscountFlag'     => $settings->isOtDiscountFlag(),
            'graduatedPrices'    => $settings->isGraduatedPrices(),
            'showPrice'          => $settings->isShowPrice(),
            'showPriceTax'       => $settings->isShowPriceTax(),
            'addTaxOt'           => $settings->isAddTaxOt(),
            'discountAttributes' => $settings->isDiscountAttributes(),
            'fsk18Purchasable'   => $settings->isFsk18Purchasable(),
            'fsk18Display'       => $settings->isFsk18Display(),
            'writeReviews'       => $settings->isWriteReviews(),
            'readReviews'        => $settings->isReadReviews()
        ];
    }
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupSettingsInterface $settings Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupSettingsInterface $settings)
    {
        return json_encode($this->serialize($settings), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param \StringType $settings Content to be deserialize.
     *
     * @return \CustomerGroupSettings
     */
    public function deserialize(StringType $settings)
    {
        $settings = json_decode($settings->asString(), true);
        
        return $this->factory->createSettings(new BoolType($settings['public']),
                                              new BoolType($settings['otDiscountFlag']),
                                              new BoolType($settings['graduatedPrices']),
                                              new BoolType($settings['showPrice']),
                                              new BoolType($settings['showPriceTax']),
                                              new BoolType($settings['addTaxOt']),
                                              new BoolType($settings['discountAttributes']),
                                              new BoolType($settings['fsk18']),
                                              new BoolType($settings['fsk18Display']),
                                              new BoolType($settings['writeReviews']),
                                              new BoolType($settings['readReviews']));
    }
}