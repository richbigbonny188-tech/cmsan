<?php
/* --------------------------------------------------------------
   OrderJsonSerializer.inc.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class OrderJsonSerializer
 *
 * This class will serialize and deserialize an Order entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class OrderJsonSerializer extends AbstractJsonSerializer
{
    /**
     * Serialize an Order object to a JSON string.
     *
     * @param OrderInterface $object           Object instance to be serialized.
     * @param bool           $encode           (optional) Whether to json_encode the result of the method (default
     *                                         true). Sometimes it might be required to encode an array of multiple
     *                                         customer records together and not one by one.
     *
     * @return string|array Returns the json encoded order (string) or an array that can be easily encoded into a
     *                      JSON string.
     * @throws InvalidArgumentException If the provided object type is invalid.
     */
    public function serialize($object, $encode = true)
    {
        if (!is_a($object, 'OrderInterface')) {
            throw new InvalidArgumentException('Invalid argument provided, OrderInterface object required: '
                                               . get_class($object));
        }
        
        $order = [
            'id'            => $object->getOrderId(),
            'statusId'      => $object->getStatusId(),
            'purchaseDate'  => $object->getPurchaseDateTime()->format('Y-m-d H:i:s'),
            'currencyCode'  => $object->getCurrencyCode()->getCode(),
            'languageCode'  => (string)$object->getLanguageCode(),
            'comment'       => $object->getComment(),
            'totalWeight'   => $object->getTotalWeight(),
            'paymentType'   => [
                'title'  => $object->getPaymentType()->getTitle(),
                'module' => $object->getPaymentType()->getModule()
            ],
            'shippingType'  => [
                'title'  => $object->getShippingType()->getTitle(),
                'module' => $object->getShippingType()->getModule()
            ],
            'customer'      => [
                'id'     => $object->getCustomerId(),
                'number' => $object->getCustomerNumber(),
                'email'  => $object->getCustomerEmail(),
                'phone'  => $object->getCustomerTelephone(),
                'vatId'  => $object->getVatIdNumber(),
                'status' => [
                    'id'       => $object->getCustomerStatusInformation()->getStatusId(),
                    'name'     => $object->getCustomerStatusInformation()->getStatusName(),
                    'image'    => $object->getCustomerStatusInformation()->getStatusImage(),
                    'discount' => $object->getCustomerStatusInformation()->getStatusDiscount(),
                    'isGuest'  => $object->getCustomerStatusInformation()->isGuest(),
                ]
            ],
            'addresses'     => [
                'customer' => $this->serializeAddress($object->getCustomerAddress()),
                'billing'  => $this->serializeAddress($object->getBillingAddress()),
                'delivery' => $this->serializeAddress($object->getDeliveryAddress())
            ],
            'items'         => [],
            'totals'        => [],
            'statusHistory' => [],
            'addonValues'   => $this->_serializeAddonValues($object->getAddonValues())
        ];
        
        foreach ($object->getOrderItems()->getArray() as $orderItem) {
            $order['items'][] = $this->serializeOrderItem($orderItem);
        }
        
        foreach ($object->getOrderTotals()->getArray() as $orderTotal) {
            $order['totals'][] = $this->serializeOrderTotal($orderTotal);
        }
        
        foreach ($object->getStatusHistory()->getArray() as $statusHistoryListItem) {
            $order['statusHistory'][] = $this->serializeOrderStatusHistoryListItem($statusHistoryListItem);
        }
        
        return ($encode) ? $this->jsonEncode($order) : $order;
    }
    
    
    /**
     * Deserialize an Order JSON String.
     *
     * @param string $string     JSON string that contains the data of the address.
     * @param object $baseObject (optional) If provided, this will be the base object to be updated
     *                           and no new instance will be created.
     *
     * @return GXEngineOrder Returns the deserialized Order object.
     * @throws InvalidArgumentException If the argument is not a string or is empty.
     */
    public function deserialize($string, $baseObject = null)
    {
        if (!is_string($string) || empty($string)) {
            throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
        }
        
        $json = json_decode($string); // error for malformed json strings
        
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
        }
        
        if (!$baseObject) {
            $order = MainFactory::create('GXEngineOrder');
        } else {
            $order = $baseObject;
        }
        
        // Deserialize JSON String
        
        if (property_exists($json, 'id') && $json->id !== null) {
            $order->setOrderId(new IdType($json->id));
        }
        
        if (property_exists($json, 'statusId') && $json->statusId !== null) {
            $order->setStatusId(new IdType($json->statusId));
        }
        
        if (property_exists($json, 'purchaseDate') && $json->purchaseDate !== null) {
            $order->setPurchaseDateTime(new EmptyDateTime($json->purchaseDate));
        }
        
        if (property_exists($json, 'currencyCode') && $json->currencyCode !== null) {
            $order->setCurrencyCode(MainFactory::create('CurrencyCode', new NonEmptyStringType($json->currencyCode)));
        }
        
        if (property_exists($json, 'languageCode') && $json->languageCode !== null) {
            $order->setLanguageCode(new LanguageCode(new NonEmptyStringType($json->languageCode)));
        }
        
        if (property_exists($json, 'totalWeight') && $json->totalWeight !== null) {
            $order->setTotalWeight(new DecimalType($json->totalWeight));
        }
        
        if (property_exists($json, 'comment') && $json->comment !== null) {
            $order->setComment(new StringType($json->comment));
        }
        
        if (property_exists($json, 'paymentType') && $json->paymentType !== null) {
            $orderPaymentType = MainFactory::create('OrderPaymentType',
                                                    new StringType($json->paymentType->title),
                                                    new StringType($json->paymentType->module));
            $order->setPaymentType($orderPaymentType);
        }
        
        if (property_exists($json, 'shippingType') && $json->shippingType !== null) {
            $orderPaymentType = MainFactory::create('OrderShippingType',
                                                    new StringType($json->shippingType->title),
                                                    new StringType($json->shippingType->module));
            $order->setShippingType($orderPaymentType);
        }
        
        if (property_exists($json, 'customer') && $json->customer !== null) {
            if (property_exists($json->customer, 'id') && $json->customer->id !== null) {
                $order->setCustomerId(new IdType($json->customer->id));
            }
            
            if (property_exists($json->customer, 'number') && $json->customer->number !== null) {
                $order->setCustomerNumber(new StringType($json->customer->number));
            }
            
            if (property_exists($json->customer, 'email') && $json->customer->email !== null) {
                $order->setCustomerEmail(new EmailStringType($json->customer->email));
            }
            
            if (property_exists($json->customer, 'phone') && $json->customer->phone !== null) {
                $order->setCustomerTelephone(new StringType($json->customer->phone));
            }
            
            if (property_exists($json->customer, 'vatId') && $json->customer->vatId !== null) {
                $order->setVatIdNumber(new StringType($json->customer->vatId));
            }
            
            if (property_exists($json->customer, 'status') && $json->customer->status !== null) {
                $statusId       = new IdType($json->customer->status->id);
                $statusName     = new StringType($json->customer->status->name);
                $statusImage    = new StringType($json->customer->status->image);
                $statusDiscount = new DecimalType($json->customer->status->discount);
                $isGuest        = new BoolType($json->customer->status->isGuest);
                
                $customerStatusInformation = MainFactory::create('CustomerStatusInformation',
                                                                 $statusId,
                                                                 $statusName,
                                                                 $statusImage,
                                                                 $statusDiscount,
                                                                 $isGuest);
                
                $order->setCustomerStatusInformation($customerStatusInformation);
            }
        }
        
        if (property_exists($json, 'addresses') && $json->addresses !== null) {
            if (property_exists($json->addresses, 'customer') && $json->addresses->customer !== null) {
                $order->setCustomerAddress($this->deserializeAddress($json->addresses->customer));
            }
            
            if (property_exists($json->addresses, 'billing') && $json->addresses->billing !== null) {
                $order->setBillingAddress($this->deserializeAddress($json->addresses->billing));
            }
            
            if (property_exists($json->addresses, 'delivery') && $json->addresses->delivery !== null) {
                $order->setDeliveryAddress($this->deserializeAddress($json->addresses->delivery));
            }
        }
        
        if (property_exists($json, 'items') && $json->items !== null) {
            $itemsArray = [];
            foreach ($json->items as $item) {
                $itemsArray[] = $this->deserializeOrderItem($item);
            }
            $orderItemCollection = MainFactory::create('OrderItemCollection', $itemsArray);
            $order->setOrderItems($orderItemCollection);
        }
        
        if (property_exists($json, 'totals') && $json->totals !== null) {
            $totalsArray = [];
            foreach ($json->totals as $total) {
                $totalsArray[] = $this->deserializeOrderTotal($total);
            }
            $orderTotalCollection = MainFactory::create('OrderTotalCollection', $totalsArray);
            $order->setOrderTotals($orderTotalCollection);
        }
        
        if (property_exists($json, 'addonValues') && $json->addonValues !== null) {
            $orderAddonValuesArray      = $this->_deserializeAddonValues($json->addonValues);
            $orderAddonValuesCollection = MainFactory::create('EditableKeyValueCollection', $orderAddonValuesArray);
            
            $order->addAddonValues($orderAddonValuesCollection);
        }
        
        if (property_exists($json, 'statusId') && $json->statusId !== null) {
            $order->setStatusId(new IdType($json->statusId));
        }
        
        return $order;
    }
    
    
    public function serializeOrderItem(OrderItemInterface $orderItem)
    {
        $result = [
            'model'                   => $orderItem->getProductModel(),
            'name'                    => $orderItem->getName(),
            'quantity'                => $orderItem->getQuantity(),
            'price'                   => $orderItem->getPrice(),
            'finalPrice'              => $orderItem->getFinalPrice(),
            'tax'                     => $orderItem->getTax(),
            'isTaxAllowed'            => $orderItem->isTaxAllowed(),
            'discount'                => $orderItem->getDiscountMade(),
            'shippingTimeInformation' => $orderItem->getShippingTimeInfo(),
            'checkoutInformation'     => $orderItem->getCheckoutInformation(),
            'quantityUnitName'        => $orderItem->getQuantityUnitName(),
            'attributes'              => [],
            'downloadInformation'     => [],
            'gxCustomizerData'        => [],
            'addonValues'             => $this->_serializeAddonValues($orderItem->getAddonValues())
        ];
        
        foreach ($orderItem->getAttributes()->getArray() as $orderItemAttribute) {
            $result['attributes'][] = $this->serializeAttribute($orderItemAttribute);
        }
        
        foreach ($orderItem->getDownloadInformation()->getArray() as $orderItemDownloadInformation) {
            $result['downloadInformation'][] = $this->_serializeOrderItemDownloadInformation($orderItemDownloadInformation);
        }
        
        $result['gxCustomizerData'] = $this->_serializeOrderItemGXCustomizerDataLegacy($orderItem->getGXCustomizerData()
                                                                                           ->getArray());
        $result['customizationData'] = $this->_serializeOrderItemGXCustomizerData($orderItem->getGXCustomizerData()
                                                                                     ->getArray());
        
        if (method_exists($orderItem, 'getOrderItemId')) {
            $result = array_merge(['id' => $orderItem->getOrderItemId()], $result);
        }
        
        return $result;
    }
    
    
    public function serializeOrderTotal(OrderTotalInterface $orderTotal)
    {
        $result = [
            'title'     => $orderTotal->getTitle(),
            'value'     => $orderTotal->getValue(),
            'valueText' => $orderTotal->getValueText(),
            'class'     => $orderTotal->getClass(),
            'sortOrder' => $orderTotal->getSortOrder()
        ];
        
        if (method_exists($orderTotal, 'getOrderTotalId')) {
            $result = array_merge(['id' => $orderTotal->getOrderTotalId()], $result);
        }
        
        return $result;
    }
    
    
    public function serializeAddress(AddressBlockInterface $addressBlock)
    {
        $result = [
            'gender'                => (string)$addressBlock->getGender(),
            'firstname'             => (string)$addressBlock->getFirstname(),
            'lastname'              => (string)$addressBlock->getLastname(),
            'company'               => (string)$addressBlock->getCompany(),
            'street'                => (string)$addressBlock->getStreet(),
            'houseNumber'           => (string)$addressBlock->getHouseNumber(),
            'additionalAddressInfo' => (string)$addressBlock->getAdditionalAddressInfo(),
            'suburb'                => (string)$addressBlock->getSuburb(),
            'postcode'              => (string)$addressBlock->getPostcode(),
            'city'                  => (string)$addressBlock->getCity(),
            'countryId'             => (int)(string)$addressBlock->getCountry()->getId(),
            'zoneId'                => (int)(string)$addressBlock->getCountryZone()->getId(),
            'b2bStatus'             => $addressBlock->getB2BStatus()->getStatus()
        ];
        
        return $result;
    }
    
    
    public function serializeAttribute(OrderItemAttributeInterface $orderItemAttribute)
    {
        $result = [
            'name'          => $orderItemAttribute->getName(),
            'value'         => $orderItemAttribute->getValue(),
            'price'         => $orderItemAttribute->getPrice(),
            'priceType'     => $orderItemAttribute->getPriceType(),
            'optionId'      => null,
            'optionValueId' => null,
            'combisId'      => null
        ];
        
        if (method_exists($orderItemAttribute, 'getCombisId')) {
            $result['combisId'] = $orderItemAttribute->getCombisId();
        }
        
        if (method_exists($orderItemAttribute, 'getOptionId')) {
            $result['optionId'] = $orderItemAttribute->getOptionId();
        }
        
        if (method_exists($orderItemAttribute, 'getOptionValueId')) {
            $result['optionValueId'] = $orderItemAttribute->getOptionValueId();
        }
        
        if (method_exists($orderItemAttribute, 'getOrderItemAttributeId')) {
            $result = array_merge(['id' => $orderItemAttribute->getOrderItemAttributeId()], $result);
        }
        
        return $result;
    }
    
    
    public function deserializeOrderItem($json, $baseObject = null)
    {
        if ($baseObject === null) {
            $orderItem = (property_exists($json, 'id') && $json->id !== null) ? MainFactory::create('StoredOrderItem',
                                                                    new IdType($json->id)) : MainFactory::create('OrderItem',
                                                                                                                 new StringType($json->name));
        } else {
            $orderItem = $baseObject;
        }
        
        if ($json->model !== null) {
            $orderItem->setProductModel(new StringType($json->model));
        }
        
        if ($json->name !== null) {
            $orderItem->setName(new StringType($json->name));
        }
        
        if ($json->quantity !== null) {
            $orderItem->setQuantity(new DecimalType($json->quantity));
        }
        
        if ($json->price !== null) {
            $orderItem->setPrice(new DecimalType($json->price));
        }
        
        if ($json->tax !== null) {
            $orderItem->setTax(new DecimalType($json->tax));
        }
        
        if ($json->isTaxAllowed !== null) {
            $orderItem->setTaxAllowed(new BoolType($json->isTaxAllowed));
        }
        
        if ($json->discount !== null) {
            $orderItem->setDiscountMade(new DecimalType($json->discount));
        }
        
        if ($json->shippingTimeInformation !== null) {
            $orderItem->setShippingTimeInfo(new StringType($json->shippingTimeInformation));
        }
        
        if ($json->checkoutInformation !== null) {
            $orderItem->setCheckoutInformation(new StringType($json->checkoutInformation));
        }
        
        if ($json->quantityUnitName !== null) {
            $orderItem->setQuantityUnitName(new StringType($json->quantityUnitName));
        }
        
        if ($json->attributes !== null) {
            $attributesArray = [];
            foreach ($json->attributes as $attribute) {
                $attributesArray[] = $this->deserializeAttribute($attribute);
            }
            $orderItemAttributeCollection = MainFactory::create('OrderItemAttributeCollection', $attributesArray);
            $orderItem->setAttributes($orderItemAttributeCollection);
        }
        
        if ($json->downloadInformation !== null && is_array($json->downloadInformation)) {
            $orderItemDownloadInformationArray = [];
            
            foreach ($json->downloadInformation as $download) {
                $orderItemDownloadInformationArray[] = $this->_deserializeOrderItemDownloadInformation($download);
            }
            
            $orderItemDownloadInformationCollection = MainFactory::create('OrderItemDownloadInformationCollection',
                                                                          $orderItemDownloadInformationArray);
            
            $orderItem->setDownloadInformation($orderItemDownloadInformationCollection);
        }
        
        if (isset($json->customizationData) && $json->customizationData !== null) {
    
            $orderItemGXCustomizerDataCollectionArray = $this->_deserializeOrderItemGXCustomizerData((array)$json->customizationData);
            $orderItemGXCustomizerDataCollection      = MainFactory::create('OrderItemGXCustomizerDataCollection',
                                                                            $orderItemGXCustomizerDataCollectionArray);
            
            $orderItem->setGXCustomizerData($orderItemGXCustomizerDataCollection);
        } elseif (isset($json->gxCustomizerData) && $json->gxCustomizerData !== null) {
    
            $gxCustomizerData                         = (array)$json->gxCustomizerData;
            $orderItemGXCustomizerDataCollectionArray = [];
    
            foreach ($gxCustomizerData as $area => $elements) {
                foreach ($elements as $element) {
                    $orderItemGXCustomizerDataCollectionArray[] = $this->_deserializeOrderItemGXCustomizerDataLegacy($area,
                                                                                                               $element);
                }
            }
    
            $orderItemGXCustomizerDataCollection = MainFactory::create('OrderItemGXCustomizerDataCollection',
                                                                       $orderItemGXCustomizerDataCollectionArray);
    
            $orderItem->setGXCustomizerData($orderItemGXCustomizerDataCollection);
        }
        
        if ($json->addonValues !== null) {
            $orderItemAddonValuesArray      = $this->_deserializeAddonValues($json->addonValues);
            $orderItemAddonValuesCollection = MainFactory::create('EditableKeyValueCollection',
                                                                  $orderItemAddonValuesArray);
            
            $orderItem->addAddonValues($orderItemAddonValuesCollection);
        }
        
        return $orderItem;
    }
    
    
    public function deserializeOrderTotal($json, $baseObject = null)
    {
        if ($baseObject === null) {
            if (property_exists($json, 'id') && $json->id !== null) {
                $orderTotal = MainFactory::create('StoredOrderTotal', new IdType($json->id));
            } else {
                $title     = new StringType(property_exists($json, 'title') ? $json->title : '');
                $value     = new DecimalType(property_exists($json, 'value') ? $json->value : 0.0);
                $valueText = (property_exists($json, 'valueText') && $json->valueText !== null) ? new StringType($json->valueText) : null;
                $class     = (property_exists($json, 'class') && $json->class !== null) ? new StringType($json->class) : null;
                $sortOrder = (property_exists($json, 'sortOrder') && $json->sortOrder !== null) ? new IntType($json->sortOrder) : null;
                
                $orderTotal = MainFactory::create('OrderTotal', $title, $value, $valueText, $class, $sortOrder);
            }
        } else {
            $orderTotal = $baseObject;
        }
        
        if ($json->title !== null) {
            $orderTotal->setTitle(new StringType($json->title));
        }
        
        if ($json->value !== null) {
            $orderTotal->setValue(new DecimalType($json->value));
        }
        
        if ($json->valueText !== null) {
            $orderTotal->setValueText(new StringType($json->valueText));
        }
        
        if ($json->class !== null) {
            $orderTotal->setClass(new StringType($json->class));
        }
        
        if ($json->sortOrder !== null) {
            $orderTotal->setSortOrder(new IntType($json->sortOrder));
        }
        
        return $orderTotal;
    }
    
    
    public function deserializeAddress($json)
    {
        $gender                = MainFactory::create('CustomerGender', $json->gender);
        $firstname             = MainFactory::create('CustomerFirstname', $json->firstname);
        $lastname              = MainFactory::create('CustomerLastname', $json->lastname);
        $company               = MainFactory::create('CustomerCompany', $json->company);
        $b2bStatus             = MainFactory::create('CustomerB2BStatus', $json->b2bStatus);
        $street                = MainFactory::create('CustomerStreet', $json->street);
        $houseNumber           = MainFactory::create('CustomerHouseNumber', $json->houseNumber);
        $additionalAddressInfo = MainFactory::create('CustomerAdditionalAddressInfo', $json->additionalAddressInfo);
        $suburb                = MainFactory::create('CustomerSuburb', $json->suburb);
        if ($json->postcode === null) {
            $json->postcode = '';
        }
        
        $postcode = MainFactory::create('CustomerPostcode', $json->postcode);
        $city     = MainFactory::create('CustomerCity', $json->city);
        
        $countryService = StaticGXCoreLoader::getService('Country');
        $country        = $countryService->getCountryById(new IdType($json->countryId));
        
        if ($json->zoneId == 0) {
            $zone = $countryService->getUnknownCountryZoneByName('');
        } else {
            $zone = $countryService->getCountryZoneById(new IdType($json->zoneId));
        }
        
        $addressBlock = MainFactory::create('AddressBlock',
                                            $gender,
                                            $firstname,
                                            $lastname,
                                            $company,
                                            $b2bStatus,
                                            $street,
                                            $houseNumber,
                                            $additionalAddressInfo,
                                            $suburb,
                                            $postcode,
                                            $city,
                                            $country,
                                            $zone);
        
        return $addressBlock;
    }
    
    
    public function deserializeAttribute($json, $baseObject = null)
    {
        
        if ($baseObject === null) {
            // Create either a OrderItemAttribute or a OrderItemProperty object.
            
            $baseClassName = ($json->combisId !== null) ? 'OrderItemProperty' : 'OrderItemAttribute';
            
            /** @var StoredOrderItemAttributeInterface $orderItemAttribute */
            $orderItemAttribute = (property_exists($json, 'id') && $json->id !== null) ? MainFactory::create('Stored' . $baseClassName,
                                                                             new IdType($json->id)) : MainFactory::create($baseClassName,
                                                                                                                          new StringType($json->name),
                                                                                                                          new StringType($json->value));
        } else {
            $orderItemAttribute = $baseObject;
        }
        
        if ($json->name !== null) {
            $orderItemAttribute->setName(new StringType($json->name));
        }
        
        if ($json->value !== null) {
            $orderItemAttribute->setValue(new StringType($json->value));
        }
        
        if ($json->price !== null) {
            $orderItemAttribute->setPrice(new DecimalType($json->price));
        }
        
        if ($json->priceType !== null) {
            $orderItemAttribute->setPriceType(new StringType($json->priceType));
        }
        
        if ($json->optionId !== null) {
            $orderItemAttribute->setOptionId(new IdType($json->optionId));
        }
        
        if ($json->optionValueId !== null) {
            $orderItemAttribute->setOptionValueId(new IdType($json->optionValueId));
        }
        
        if ($json->combisId !== null) {
            $orderItemAttribute->setCombisId(new IdType($json->combisId));
        }
        
        return $orderItemAttribute;
    }
    
    
    public function serializeOrderStatusHistoryListItem(OrderStatusHistoryListItem $orderStatusHistoryListItem)
    {
        $result = [
            'id'               => $orderStatusHistoryListItem->getOrderStatusHistoryId(),
            'statusId'         => $orderStatusHistoryListItem->getOrderStatusId(),
            'dateAdded'        => $orderStatusHistoryListItem->getDateAdded()->format('Y-m-d H:i:s'),
            'comment'          => $orderStatusHistoryListItem->getComment(),
            'customerNotified' => $orderStatusHistoryListItem->isCustomerNotified()
        ];
        
        return $result;
    }
    
    
    protected function _serializeOrderItemDownloadInformation(OrderItemDownloadInformation $downloadInformation)
    {
        $result = [
            'filename'       => $downloadInformation->getFilename(),
            'maxDaysAllowed' => $downloadInformation->getMaxDaysAllowed(),
            'countAvailable' => $downloadInformation->getCountAvailable()
        ];
        
        return $result;
    }
    
    
    protected function _deserializeOrderItemDownloadInformation($json)
    {
        $downloadInformation = MainFactory::create('OrderItemDownloadInformation',
                                                   new FilenameStringType($json->filename),
                                                   new IntType($json->maxDaysAllowed),
                                                   new IntType($json->countAvailable));
        
        return $downloadInformation;
    }
    
    
    protected function _serializeOrderItemGXCustomizerData(array $gxCustomizerDataCollectionArray)
    {
        $gxCustomizerData = [];
        foreach ($gxCustomizerDataCollectionArray as $item) {
            /** @var OrderItemGXCustomizerData $item */
            $gxCustomizerData['setId']   = $item->getSetId();
            $gxCustomizerData['setName'] = $item->getSet();
        
            if (isset($gxCustomizerData['areas']) === false) {
            
                $gxCustomizerData['areas'] = [];
            }
        
            if (isset($gxCustomizerData['areas'][$item->getArea()]) === false) {
    
                $gxCustomizerData['areas'][$item->getArea()] = [
                    'name'   => $item->getArea(),
                    'areaId' => $item->getAreaId(),
                ];
            }
        
            if (isset($gxCustomizerData['areas'][$item->getArea()]['elements']) === false) {
            
                $gxCustomizerData['areas'][$item->getArea()]['elements'] = [];
            }
    
            $gxCustomizerData['areas'][$item->getArea()]['elements'][] = [
                'elementId' => $item->getElementId(),
                'name'      => $item->getName(),
                'value'     => $item->getValue(),
                'type'      => $item->getType(),
                'file'      => $item->getFile(),
            ];
        }
    
        if (isset($gxCustomizerData['areas'])) {
        
            $gxCustomizerData['areas'] = array_values($gxCustomizerData['areas']);
        }
    
        return $gxCustomizerData;
    }
    
    
    /**
     * @param array $data
     *
     * @return array
     */
    protected function _deserializeOrderItemGXCustomizerData(array $data)
    {
        $result = [];
    
        [
            'setId'   => $setId,
            'setName' => $setName,
            'areas'   => $areas,
        ] = $data;
    
        foreach ($areas as $area) {
            
            foreach ($area->elements as $element) {
            
                $result[] = MainFactory::create('OrderItemGXCustomizerData',
                                                new StringType($setName),
                                                new StringType($area->name),
                                                new StringType($element->type),
                                                new StringType($element->name),
                                                new StringType($element->value),
                                                new StringType($element->file),
                                                $setId !== null ? new IdType($setId) : null,
                                                $element->elementId ? new IdType($element->elementId) : null,
                                                $area->areaId ? new IdType($area->areaId) : null);
            }
        }
    
        return $result;
    }
    
    
    /**
     * Returns the data for the gxCustomizerData
     *
     * @param array $gxCustomizerDataCollectionArray
     *
     * @return array|OrderItemGXCustomizerData
     *
     * @deprecated will be removed at some point use the data of "customizationData" instead of "gxCustomizerData"
     */
    protected function _serializeOrderItemGXCustomizerDataLegacy(array $gxCustomizerDataCollectionArray)
    {
        $gxCustomizerData = [];
        
        /** @var $gxCustomizerData OrderItemGXCustomizerData */
        foreach ($gxCustomizerDataCollectionArray as $gxCustomizerDataCollectionItem) {
            $gxCustomizerData[$gxCustomizerDataCollectionItem->getArea()][] = [
                'type'  => $gxCustomizerDataCollectionItem->getType(),
                'name'  => $gxCustomizerDataCollectionItem->getName(),
                'value' => $gxCustomizerDataCollectionItem->getValue(),
                'file'  => $gxCustomizerDataCollectionItem->getFile(),
            ];
        }
        
        return $gxCustomizerData;
    }
    
    
    /**
     * deserialize gx customizer data
     *
     * @param array $data
     *
     * @return OrderItemGXCustomizerData
     *
     * @deprecated will be removed at some point use the data of "customizationData" instead of "gxCustomizerData"
     */
    protected function _deserializeOrderItemGXCustomizerDataLegacy($area, $elementData): OrderItemGXCustomizerData
    {
        return MainFactory::create('OrderItemGXCustomizerData',
                                                new StringType(''),
                                                new StringType($area),
                                                new StringType($elementData->type),
                                                new StringType($elementData->name),
                                                new StringType($elementData->value),
                                                new StringType($elementData->file));
    }
}