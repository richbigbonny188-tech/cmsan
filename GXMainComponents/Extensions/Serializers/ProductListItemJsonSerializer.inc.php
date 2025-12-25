<?php

/* --------------------------------------------------------------
   ProductListItemJsonSerializer.inc.php 2018-02-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class ProductListItemJsonSerializer
 *
 * This class will serialize and deserialize an ProductListItem entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communications.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class ProductListItemJsonSerializer extends AbstractJsonSerializer
{
    /**
     * Serialize an ProductListItem object to a JSON string.
     *
     * @param ProductListItem $object          Object instance to be serialized.
     * @param bool            $encode          (optional) Whether to json_encode the result of the method (default
     *                                         true).
     *
     * @return string|array Returns the json encoded product list item (string) or an array that can be easily encoded
     *                      into a JSON string.
     * @throws InvalidArgumentException If the provided object type is invalid.
     */
    public function serialize($object, $encode = true)
    {
        if (!is_a($object, 'ProductListItem')) {
            throw new InvalidArgumentException('Invalid argument provided, ProductListItem object required: '
                                               . get_class($object));
        }
        
        $productListItem = [
            'id'              => $object->getProductId(),
            'isActive'        => $object->isActive(),
            'sortOrder'       => $object->getSortOrder(),
            'dateAdded'       => $object->getAddedDateTime()->format('Y-m-d H:i:s'),
            'dateAvailable'   => $object->getAvailableDateTime()->format('Y-m-d H:i:s'),
            'lastModified'    => $object->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
            'orderedCount'    => $object->getOrderedCount(),
            'productModel'    => $object->getProductModel(),
            'ean'             => $object->getEan(),
            'price'           => $object->getPrice(),
            'discountAllowed' => $object->getDiscountAllowed(),
            'taxClassId'      => $object->getTaxClassId(),
            'quantity'        => $object->getQuantity(),
            'name'            => $object->getName(),
            'image'           => $object->getImage(),
            'imageAltText'    => $object->getImageAltText(),
            'urlKeywords'     => $object->getUrlKeywords(),
            'weight'          => $object->getWeight(),
            'shippingCosts'   => $object->getShippingCosts(),
            'shippingTimeId'  => $object->getShippingTimeId(),
            'productTypeId'   => $object->getProductTypeId(),
            'manufacturerId'  => $object->getManufacturerId(),
            'quantityUnitId'  => $object->getQuantityUnitId(),
            'isFsk18'         => $object->isFsk18(),
            'isVpeActive'     => $object->isVpeActive(),
            'vpeId'           => $object->getVpeId(),
            'vpeValue'        => $object->getVpeValue(),
            'specialOfferId'  => $object->getSpecialOfferId(),
            'mainCategoryId'  => $object->getMainCategoryId(),
        ];
        
        return ($encode) ? $this->jsonEncode($productListItem) : $productListItem;
    }
    
    
    /**
     * Deserialize method is not used by the api.
     *
     * @param string $string     JSON string that contains the data of the address.
     * @param object $baseObject (optional) This parameter is not supported for this serializer because the
     *                           ProductListItem does not have any setter methods.
     *
     * @throws RuntimeException If the argument is not a string or is empty.
     */
    public function deserialize($string, $baseObject = null)
    {
        throw new RuntimeException('Method is not implemented.');
    }
}