<?php

/* --------------------------------------------------------------
   CustomerGroupSerializerInterface.php 2017-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupSerializerInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
interface CustomerGroupSerializerInterface
{
    /**
     * Serialize a value to a JSON string.
     *
     * @param \CustomerGroupInterface $customerGroup Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupInterface $customerGroup);
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupInterface $customerGroup Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupInterface $customerGroup);
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param \StringType $customerGroup Content to be deserialize.
     *
     * @return \CustomerGroupInterface
     */
    public function deserialize(StringType $customerGroup);
}