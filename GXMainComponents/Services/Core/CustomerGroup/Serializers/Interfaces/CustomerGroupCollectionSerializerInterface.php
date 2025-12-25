<?php
/* --------------------------------------------------------------
   CustomerGroupCollectionSerializerInterface.php 2017-09-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupCollectionSerializerInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Serializers
 */
interface CustomerGroupCollectionSerializerInterface
{
    /**
     * Serialize a value to a JSON string.
     *
     * @param \CustomerGroupCollection $collection Content to be serialized.
     *
     * @return array
     */
    public function serialize(CustomerGroupCollection $collection);
    
    
    /**
     * JSON Encode Wrapper.
     *
     * @param \CustomerGroupCollection $collection Content to be encoded.
     *
     * @return string Returns the encoded JSON string that represents the data.
     */
    public function encode(CustomerGroupCollection $collection);
    
    
    /**
     * Deserialize a JSON string.
     *
     * @param \StringType $collection Content to be deserialize.
     *
     * @return \CustomerGroupCollection
     */
    public function deserialize(StringType $collection);
}