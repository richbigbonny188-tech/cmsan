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
 * Interface CustomerGroupSettingsSerializerInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
interface CustomerGroupSettingsSerializerInterface
{
    /**
     * Serialize a value to a JSON string.
     *
     * @param \CustomerGroupSettingsInterface $settings Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupSettingsInterface $settings);
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupSettingsInterface $settings Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupSettingsInterface $settings);
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param \StringType $settings Content to be deserialize.
     *
     * @return \CustomerGroupSettingsInterface
     */
    public function deserialize(StringType $settings);
}