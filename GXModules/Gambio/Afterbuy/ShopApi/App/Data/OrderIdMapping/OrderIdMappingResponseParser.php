<?php
/* --------------------------------------------------------------
   OrderIdMappingResponseParser.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\App\Data\OrderIdMapping;

use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdMappingException;
use GXModules\Gambio\Afterbuy\ShopApi\Model\AfterbuyOrderIdMapping;
use SimpleXMLElement;

/**
 * Class OrderIdMappingResponseParser
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\App\Data
 */
class OrderIdMappingResponseParser
{
    private const MANAGEABLE_SUCCESS_VALUES = ['1', '0'];
    
    
    private const MESSAGE_ALREADY_TRANSMITTED = 'Diese Bestellung wurde bereits erfasst';
    
    
    /**
     * @param string $xmlResponse
     *
     * @return AfterbuyOrderIdMapping
     * @throws AfterbuyOrderIdMappingException
     */
    public function parse(string $xmlResponse): AfterbuyOrderIdMapping
    {
        $xmlData = $this->validateXml($xmlResponse);
        $xml     = $xmlData['xml'];
        $success = $xmlData['success'];
        
        if ($success === '1') {
            return $this->parseSuccessResponse($xml, $xmlResponse);
        }
        
        return $this->parseErrorResponse($xml, $xmlResponse);
    }
    
    
    /**
     * Parses the Afterbuy Shop-API (legacy) xml error response, which might contain the shops- and Afterbuy order id.
     * This is the case e.g. if the order has already been entered.
     *
     * @param SimpleXMLElement $xml
     * @param string           $xmlResponse
     *
     * @return AfterbuyOrderIdMapping
     * @throws AfterbuyOrderIdMappingException
     */
    private function parseErrorResponse(SimpleXMLElement $xml, string $xmlResponse): AfterbuyOrderIdMapping
    {
        $errorList = $this->tryGetElement($xml, 'errorlist');
        if ($errorList === null) {
            $message = "Afterbuy response missing '<errorlist>' element which might contain the Afterbuy- and Shop order id.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse);
        }
        
        $shopOrderId     = $this->tryGetElementAsString($errorList, 'VID');
        $afterbuyOrderId = $this->tryGetElementAsString($errorList, 'OrderID');
        
        $shopId     = $shopOrderId !== null ? (int)$shopOrderId : null;
        $afterbuyId = $afterbuyOrderId !== null ? (int)$afterbuyOrderId : null;
        
        if ($shopOrderId === null) {
            $message = "Afterbuy response missing '<VID>' element in <errorlist> block.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse, $shopId, $afterbuyId);
        }
        if ($afterbuyOrderId === null) {
            $message = "Afterbuy response missing '<OrderID>' element in <errorlist> block.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse, $shopId, $afterbuyId);
        }
        $isTransmitted = $this->isAlreadyTransmitted($errorList);
        
        return new AfterbuyOrderIdMapping((int)$shopOrderId, (int)$afterbuyOrderId, $isTransmitted);
    }
    
    
    /**
     * Checks <errorlist> xml element to contain an error message that
     * the order was already transmitted.
     *
     * @param SimpleXMLElement $errorList
     *
     * @return true|null
     */
    private function isAlreadyTransmitted(SimpleXMLElement $errorList): ?bool
    {
        $errorMessage = $this->tryGetElementAsString($errorList, 'error');
        if ($errorMessage === null) {
            return null;
        }
        
        $result = stripos($errorMessage, self::MESSAGE_ALREADY_TRANSMITTED) !== false;
        if ($result) {
            return true;
        }
        
        return null;
    }
    
    
    /**
     * Parses Afterbuy Shop-API (legacy) xml response which contained `<success>` = '1',
     * extracting the Shop- and Afterbuy order id.
     *
     * @param SimpleXMLElement $xml
     * @param string           $xmlResponse
     *
     * @return AfterbuyOrderIdMapping
     * @throws AfterbuyOrderIdMappingException
     */
    private function parseSuccessResponse(SimpleXMLElement $xml, string $xmlResponse): AfterbuyOrderIdMapping
    {
        $data = $this->tryGetElement($xml, 'data');
        if ($data === null) {
            $message = "Afterbuy response missing '<data>' element.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse);
        }
        
        $shopOrderId     = $this->tryGetElementAsString($data, 'VID');
        $afterbuyOrderId = $this->tryGetElementAsString($data, 'AID');
        
        $shopId     = $shopOrderId !== null ? (int)$shopOrderId : null;
        $afterbuyId = $afterbuyOrderId !== null ? (int)$afterbuyOrderId : null;
        
        if ($shopOrderId === null) {
            $message = "Afterbuy response missing '<VID>' element in <data> block.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse, $shopId, $afterbuyId);
        }
        
        if ($afterbuyOrderId === null) {
            $message = "Afterbuy response missing '<AID>' element in <data> block.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse, $shopId, $afterbuyId);
        }
        
        return new AfterbuyOrderIdMapping((int)$shopOrderId, (int)$afterbuyOrderId);
    }
    
    
    /**
     * Validates the xml response.
     *
     * This method throws exceptions if the xml is invalid and can't be parsed, the `<success>` element
     * is missing or don't contain the values '0' or '1'.
     *
     * The value '0' is also allowed, because the response may contain the afterbuy order id and shop
     * order id in the error list of the xml response.
     *
     * @param string $xmlResponse
     *
     * @return array{xml: SimpleXMLElement, success: string}
     * @throws AfterbuyOrderIdMappingException
     */
    private function validateXml(string $xmlResponse): array
    {
        $xml = simplexml_load_string($xmlResponse);
        if ($xml === false) {
            $message = "Response from Afterbuy could not be parsed:\n===\n$xmlResponse\n===\n";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse);
        }
        
        $success = $this->tryGetElementAsString($xml, 'success');
        if ($success === null) {
            $message = "Afterbuy response missing '<success>' element.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse);
        }
        if (!in_array($success, self::MANAGEABLE_SUCCESS_VALUES)) {
            $message = "Invalid '<success>' tag value '$success'. Value 1 can be handled on all cases, value 0 in most cases.";
            throw new AfterbuyOrderIdMappingException($message, $xmlResponse);
        }
        
        return [
            'xml'     => $xml,
            'success' => $success,
        ];
    }
    
    
    /**
     * Tries to get a property as string from the given xml.
     * Returns null if the property was not found or can't be used as string.
     *
     * @param SimpleXMLElement $xml
     * @param string           $property
     *
     * @return string|null
     */
    private function tryGetElementAsString(SimpleXMLElement $xml, string $property): ?string
    {
        if (property_exists($xml, $property) && method_exists($xml->$property, '__toString')) {
            return $xml->$property->__toString();
        }
        
        return null;
    }
    
    
    /**
     * Tries to get a property as `SimpleXMLElement` from the given xml.
     * Returns null if the property was not found or is not of type `SimpleXMLElement`
     *
     * @param SimpleXMLElement $xml
     * @param string           $property
     *
     * @return SimpleXMLElement|null
     */
    private function tryGetElement(SimpleXMLElement $xml, string $property): ?SimpleXMLElement
    {
        if (property_exists($xml, $property) && $xml->$property instanceof SimpleXMLElement) {
            return $xml->$property;
        }
        
        return null;
    }
}