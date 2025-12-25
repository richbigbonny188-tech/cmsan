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
 * Interface CustomerGroupConfigurationsSerializerInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
interface CustomerGroupConfigurationsSerializerInterface
{
    /**
     * Serialize a value to a JSON string.
     *
     * @param \CustomerGroupConfigurationsInterface $configurations Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupConfigurationsInterface $configurations);
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupConfigurationsInterface $configuration Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupConfigurationsInterface $configuration);
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param StringType $configurations Content to be deserialize.
     *
     * @return \CustomerGroupConfigurationsInterface
     */
    public function deserialize(StringType $configurations);
}
