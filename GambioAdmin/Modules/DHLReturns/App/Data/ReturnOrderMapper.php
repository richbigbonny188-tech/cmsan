<?php
/* --------------------------------------------------------------
   ReturnOrderMapper.php 2021-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\App\Data;

use Gambio\Admin\Modules\DHLReturns\Model\Collections\CustomsDocumentsPositionCollection;
use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\Country;
use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\CustomsDocument;
use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\CustomsDocumentPosition;
use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\ReturnOrder;
use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\SimpleAddress;

class ReturnOrderMapper
{
    public static function mapFromArrayToObject(
        array $inputArray,
        string $receiverId,
        string $email,
        string $telephoneNumber
    ): ReturnOrder {
        $inputSenderAddress = $inputArray['senderAddress'];
        $senderAddress      = new SimpleAddress($inputSenderAddress['name1'],
                                                $inputSenderAddress['streetName'],
                                                $inputSenderAddress['houseNumber'],
                                                $inputSenderAddress['postCode'],
                                                $inputSenderAddress['city']);
        $senderAddress->setname2($inputSenderAddress['name2']);
        $senderAddress->setName3($inputSenderAddress['name3']);
        $senderAddress->setCountry(new Country($inputArray['senderAddress']['country']));
        $returnOrder = new ReturnOrder($receiverId, $senderAddress);
        $returnOrder->setCustomerReference($inputArray['customerReference']);
        $returnOrder->setShipmentReference($inputArray['shipmentReference']);
        $returnOrder->setEmail($email);
        $returnOrder->setTelephoneNumber($telephoneNumber);
        $returnOrder->setWeightInGrams((int)$inputArray['weightInGrams']);
        $returnOrder->setValue((float)$inputArray['value']);
        if (isset($inputArray['customsDocument'])) {
            $returnOrder->setCustomsDocument(static::mapCustomsDocumentFromArrayToObject($inputArray['customsDocument']));
        }
        
        return $returnOrder;
    }
    
    
    protected static function mapCustomsDocumentFromArrayToObject(array $inputDocument): CustomsDocument
    {
        $customsDocument = new CustomsDocument($inputDocument['currency'],
                                               static::mapCustomsDocumentPositionsFromArrayToCollection($inputDocument['positions']));
        $customsDocument->setOriginalInvoiceDate($inputDocument['originalInvoiceDate']);
        $customsDocument->setOriginalInvoiceNumber($inputDocument['originalInvoiceNumber']);
        $customsDocument->setAcommpanyingDocument($inputDocument['acommpanyingDocument']);
        $customsDocument->setOriginalOperator($inputDocument['originalOperator']);
        $customsDocument->setOriginalShipmentNumber($inputDocument['originalShipmentNumber']);
        $customsDocument->setComment($inputDocument['comment']);
        
        return $customsDocument;
    }
    
    
    protected static function mapCustomsDocumentPositionsFromArrayToCollection(array $positions
    ): CustomsDocumentsPositionCollection {
        $collection = new CustomsDocumentsPositionCollection();
        foreach ($positions as $position) {
            if ((int)$position['count'] <= 0) {
                continue;
            }
            $customsDocumentPosition = new CustomsDocumentPosition($position['positionDescription'],
                                                                   (int)$position['count'],
                                                                   (int)$position['weightInGrams'],
                                                                   (float)$position['values']);
            $customsDocumentPosition->setArticleReference($position['articleReference']);
            $customsDocumentPosition->setOriginCountry($position['originCountry']);
            $customsDocumentPosition->setTarifNumber($position['tarifNumber']);
            $collection->addCustomsDocumentPosition($customsDocumentPosition);
        }
        
        return $collection;
    }
    
    
    public static function mapFromObjectToArray(ReturnOrder $returnOrder): array
    {
        return [
            'receiverId'         => mb_substr($returnOrder->getReceiverId(), 0, 30),
            'customerReference'  => mb_substr($returnOrder->getCustomerReference(), 0, 30),
            'shipmentReference'  => mb_substr($returnOrder->getShipmentReference(), 0, 30),
            'senderAddress'      => static::mapSimpleAddressFromObjectToArray($returnOrder->getSenderAddress()),
            'email'              => mb_substr($returnOrder->getEmail(), 0, 70),
            'telephoneNumber'    => mb_substr($returnOrder->getTelephoneNumber(), 0, 35),
            'weightInGrams'      => $returnOrder->getWeightInGrams(),
            'value'              => $returnOrder->getValue(),
            'customsDocument'    => static::mapCustomsDocumentFromObjectToArray($returnOrder->getCustomsDocument()),
            'returnDocumentType' => $returnOrder->getReturnDocumentType(),
        ];
    }
    
    
    protected static function mapSimpleAddressFromObjectToArray(SimpleAddress $simpleAddress): array
    {
        $outputArray = [
            'name1'       => mb_substr($simpleAddress->getName1(), 0, 35),
            'name2'       => mb_substr($simpleAddress->getName2(), 0, 35),
            'name3'       => mb_substr($simpleAddress->getName3(), 0, 35),
            'streetName'  => mb_substr($simpleAddress->getStreetName(), 0, 35),
            'houseNumber' => mb_substr($simpleAddress->getHouseNumber(), 0, 5),
            'postCode'    => mb_substr($simpleAddress->getPostCode(), 0, 10),
            'city'        => mb_substr($simpleAddress->getCity(), 0, 35),
        ];
        
        if (($country = $simpleAddress->getCountry()) !== null) {
            $outputArray['country'] = [
                'countyIsoCode' => mb_substr($country->getCountryISOCode(), 0, 3),
                'country'       => mb_substr($country->getCountry(), 0, 30),
                'state'         => mb_substr($country->getState(), 0, 30),
            ];
        }
        
        return $outputArray;
    }
    
    
    protected static function mapCustomsDocumentFromObjectToArray(CustomsDocument $customsDocument): array
    {
        $customsDocumentArray = [
            'currency'               => mb_substr($customsDocument->getCurrency(), 0, 3),
            'originalShipmentNumber' => mb_substr($customsDocument->getOriginalShipmentNumber(), 0, 35),
            'originalOperator'       => mb_substr($customsDocument->getOriginalOperator(), 0, 40),
            'acommpanyingDocument'   => mb_substr($customsDocument->getAcommpanyingDocument(), 0, 35),
            'originalInvoiceNumber'  => mb_substr($customsDocument->getOriginalInvoiceNumber(), 0, 35),
            'originalInvoiceDate'    => mb_substr($customsDocument->getOriginalInvoiceDate(), 0, 35),
            'comment'                => mb_substr($customsDocument->getComment(), 0, 150),
            'positions'              => static::mapCustomsDocumentPositionsFromCollectionToArray($customsDocument->getPositions()),
        ];
        
        return $customsDocumentArray;
    }
    
    
    protected static function mapCustomsDocumentPositionsFromCollectionToArray(
        CustomsDocumentsPositionCollection $collection
    ): array {
        $positions = [];
        /** @var CustomsDocumentPosition $collectionPosition */
        foreach ($collection as $collectionPosition) {
            $positions[] = [
                'positionDescription' => mb_substr($collectionPosition->getPositionDescription(), 0, 50),
                'count'               => (int)$collectionPosition->getCount(),
                'weightInGrams'       => (int)$collectionPosition->getWeightInGrams(),
                'values'              => (float)$collectionPosition->getValues(),
                'originCountry'       => mb_substr($collectionPosition->getOriginCountry(), 0, 3),
                'articleReference'    => mb_substr($collectionPosition->getArticleReference(), 0, 40),
                'tarifNumber'         => mb_substr($collectionPosition->getTarifNumber(), 0, 8),
            ];
        }
        
        return $positions;
    }
}