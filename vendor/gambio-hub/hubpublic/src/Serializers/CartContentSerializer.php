<?php
/* --------------------------------------------------------------
   CartContentSerializer.php 2023-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2023 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Serializers;

use HubPublic\Collections\CartItemCollection;
use HubPublic\Collections\CartTotalItemCollection;
use HubPublic\Exceptions\BadSerializerValueException;
use HubPublic\Serializers\Interfaces\SerializerInterface;
use HubPublic\ValueObjects\CartContent;
use HubPublic\ValueObjects\CartItem;
use HubPublic\ValueObjects\CartTotalItem;

/**
 * Class CartContentSerializer
 *
 * @package HubPublic\Serializers
 */
class CartContentSerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * Serialize a value to a JSON string or array.
     *
     * @param \HubPublic\ValueObjects\CartContent $cartContent Content to be serialized.
     * @param bool                                $encode      Serialize to string?
     *
     * @return array|string Serialized JSON string or array of given content.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If passed argument is not a CartContent.
     */
    public function serialize($cartContent, bool $encode = true)
    {
        if (!is_object($cartContent) || !($cartContent instanceof CartContent)) {
            throw new BadSerializerValueException('Argument is not a CartContent: ' . gettype($cartContent));
        }
        
        $json = [
            'cartItems'      => $this->serializeCartItems($cartContent->getCartItemCollection()),
            'totalPrice'     => $cartContent->getTotalPrice(),
            'shippingCost'   => $cartContent->getShippingCost(),
            'cartTotalItems' => $this->serializeCartTotalItems($cartContent->getCartTotalItemCollection()),
        ];
        
        return $encode ? json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $json;
    }
    
    
    /**
     * Deserializes a decoded JSON string.
     *
     * @param array $decodedJson Associative array that contains the data.
     *
     * @return \HubPublic\ValueObjects\CartContent CartContent instance that contains the deserialized data.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If array is malformed.
     */
    public function deserialize(array $decodedJson): CartContent
    {
        $this->verifyArray($decodedJson);
        
        $cartItemCollection      = $this->getCartItemCollection($decodedJson);
        $cartTotalItemCollection = $this->getCartTotalItemCollection($decodedJson);
        
        $totalPrice   = array_key_exists('totalPrice', $decodedJson) ? (float)filter_var(
            $decodedJson['totalPrice'],
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        ) : 0;
        $shippingCost = array_key_exists('shippingCost', $decodedJson) ? (float)filter_var(
            $decodedJson['shippingCost'],
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        ) : 0;
        
        return new CartContent($cartItemCollection, $totalPrice, $shippingCost, $cartTotalItemCollection);
    }
    
    
    /**
     * Creates a CartItemCollection from json cart item data.
     *
     * @param array $decodedJson Array of cart items.
     *
     * @return \HubPublic\Collections\CartItemCollection CartItemCollection instance that contains the deserialized
     *                                                   data.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If cart item information are invalid.
     * @throws \HubPublic\Exceptions\InvalidCollectionItemException If provided cart item collection item is invalid.
     */
    private function getCartItemCollection(array $decodedJson): CartItemCollection
    {
        $cartItemCollection = new CartItemCollection();
        
        if (!array_key_exists('cartItems', $decodedJson)) {
            throw new BadSerializerValueException('Array data not containing cart items.');
        }
        
        foreach ($decodedJson['cartItems'] as $cartItem) {
            $ean      = filter_var($cartItem['ean'], FILTER_SANITIZE_STRING);
            $name     = str_replace(['<', '>'], '', $cartItem['name']);
            $price    = (float)filter_var($cartItem['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $quantity = (float)filter_var(
                $cartItem['quantity'],
                FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION
            );
            
            $attributes = [];
            
            $imageUrl     = array_key_exists('imageUrl', $cartItem) ? filter_var(
                $cartItem['imageUrl'],
                FILTER_SANITIZE_URL
            ) : '';
            $categoryPath = array_key_exists('categoryPath', $cartItem) ? filter_var(
                $cartItem['categoryPath'],
                FILTER_SANITIZE_STRING
            ) : '';
            $mpn          = array_key_exists('mpn', $cartItem) ? filter_var(
                $cartItem['mpn'],
                FILTER_SANITIZE_STRING
            ) : '';
            $productUrl   = array_key_exists('productUrl', $cartItem) ? filter_var(
                $cartItem['productUrl'],
                FILTER_SANITIZE_URL
            ) : '';
            $quantityUnit = array_key_exists('quantityUnit', $cartItem) ? filter_var(
                $cartItem['quantityUnit'],
                FILTER_SANITIZE_STRING
            ) : '';
            $reference    = array_key_exists('reference', $cartItem) ? filter_var(
                $cartItem['reference'],
                FILTER_SANITIZE_STRING
            ) : '';
            $type         = array_key_exists('type', $cartItem) ? (string)(int)filter_var(
                $cartItem['type'],
                FILTER_SANITIZE_STRING
            ) : '';
            $brand        = array_key_exists('brand', $cartItem) ? filter_var(
                $cartItem['brand'],
                FILTER_SANITIZE_STRING
            ) : '';
            $tax          = array_key_exists('tax', $cartItem) ? (float)filter_var(
                $cartItem['tax'],
                FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION
            ) : 0;
            $model = array_key_exists('model', $cartItem) ? (string)filter_var($cartItem['model'],
                FILTER_SANITIZE_STRING) : '';
            
            if (array_key_exists('attributes', $cartItem)) {
                foreach ($cartItem['attributes'] as $key => $values) {
                    $attributes[$key] = str_replace(['<', '>'], '', $values);
                }
            }
            
            $cartItemCollection->add(new CartItem(
                $ean,
                $name,
                $price,
                $quantity,
                $attributes,
                $imageUrl,
                $categoryPath,
                $mpn,
                $productUrl,
                $quantityUnit,
                $reference,
                $type,
                $brand,
                $tax,
                $model
            ));
        }
        
        return $cartItemCollection;
    }
    
    
    /**
     * Creates a CartTotalItemCollection from json cart item data.
     *
     * @param array $decodedJson Array of cart items.
     *
     * @return \HubPublic\Collections\CartTotalItemCollection CartTotalItemCollection instance that contains the
     *                                                        deserialized data.
     *
     * @throws \HubPublic\Exceptions\InvalidCollectionItemException If cart total item information are invalid.
     */
    private function getCartTotalItemCollection(array $decodedJson): CartTotalItemCollection
    {
        $cartTotalItemCollection = new CartTotalItemCollection();
        
        if (array_key_exists('cartTotalItems', $decodedJson) && is_array($decodedJson['cartTotalItems'])) {
            foreach ($decodedJson['cartTotalItems'] as $cartTotalItem) {
                $code               = filter_var($cartTotalItem['code'], FILTER_SANITIZE_STRING);
                $title              = filter_var($cartTotalItem['title'], FILTER_SANITIZE_STRING);
                $value              = (float)filter_var(
                    $cartTotalItem['value'],
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                );
                $changeTotal        = (float)filter_var(
                    $cartTotalItem['changeTotal'],
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                );
                $changeTax          = (float)filter_var(
                    $cartTotalItem['changeTax'],
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                );
                $changeShippingCost = (float)filter_var(
                    $cartTotalItem['changeShippingCost'],
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                );
                
                $cartTotalItemCollection->add(new CartTotalItem(
                    $code,
                    $title,
                    $value,
                    $changeTotal,
                    $changeTax,
                    $changeShippingCost
                ));
            }
        }
        
        return $cartTotalItemCollection;
    }
    
    
    /**
     * Serialize the CartItem collection.
     *
     * @param \HubPublic\Collections\CartItemCollection $collection
     *
     * @return array
     */
    private function serializeCartItems(CartItemCollection $collection): array
    {
        $json = [];
        
        /** @var CartItem $cartItem */
        foreach ($collection->asArray() as $cartItem) {
            $json[] = [
                'ean'          => $cartItem->getEan(),
                'name'         => $cartItem->getName(),
                'price'        => $cartItem->getPrice(),
                'quantity'     => $cartItem->getQuantity(),
                'attributes'   => $cartItem->getAttributes(),
                'imageUrl'     => $cartItem->getImageUrl(),
                'categoryPath' => $cartItem->getCategoryPath(),
                'mpn'          => $cartItem->getMpn(),
                'productUrl'   => $cartItem->getProductUrl(),
                'quantityUnit' => $cartItem->getQuantityUnit(),
                'reference'    => $cartItem->getReference(),
                'type'         => $cartItem->getType(),
                'brand'        => $cartItem->getBrand(),
                'tax'          => $cartItem->getTax(),
                'model'        => $cartItem->getModel(),
            ];
        }
        
        return $json;
    }
    
    
    /**
     * Serializes a CartTotalItemCollection.
     *
     * @param \HubPublic\Collections\CartTotalItemCollection $totalItems
     *
     * @return array
     */
    private function serializeCartTotalItems(CartTotalItemCollection $totalItems): array
    {
        $json = [];
        
        /** @var \HubPublic\ValueObjects\CartTotalItem $totalItem */
        foreach ($totalItems->asArray() as $totalItem) {
            $json[] = [
                'code'               => $totalItem->getCode(),
                'title'              => $totalItem->getTitle(),
                'value'              => $totalItem->getValue(),
                'changeTotal'        => $totalItem->getChangeTotal(),
                'changeTax'          => $totalItem->getChangeTax(),
                'changeShippingCost' => $totalItem->getChangeShippingCost(),
            ];
        }
        
        return $json;
    }
}
