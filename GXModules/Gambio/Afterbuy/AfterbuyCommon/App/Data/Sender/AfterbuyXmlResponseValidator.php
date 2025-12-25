<?php
/* --------------------------------------------------------------
   AfterbuyXmlResponseValidator.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\Sender;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use SimpleXMLElement;

/**
 * Class AfterbuyXmlResponseValidator
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuySender
 */
class AfterbuyXmlResponseValidator
{
    private const CALL_STATUS_SUCCESS = 'Success';
    
    /**
     * @var AfterbuyLogger
     */
    private AfterbuyLogger $logger;
    
    
    /**
     * AfterbuyXmlResponseValidator constructor.
     *
     * @param AfterbuyLogger $logger
     */
    public function __construct(AfterbuyLogger $logger)
    {
        $this->logger = $logger;
    }
    
    
    /**
     * @param string $responseBody
     *
     * @throws AfterbuyResponseException
     */
    public function validate(string $responseBody): void
    {
        $xmlResponse = simplexml_load_string($responseBody);
        if ($xmlResponse === false) {
            $message = "Response from Afterbuy could not be parsed:\n===\n" . $responseBody . "\n===\n";
            throw new AfterbuyResponseException($message);
        }
        $callStatus = $this->getCallStatus($xmlResponse);
        if ($this->isSuccessful($callStatus)) {
            return;
        }
        $errors = $this->collectErrorsFromXML($xmlResponse);
        
        $message = "AfterbuyError - Order export failed to send data to Afterbuy. See logs for details.";
        $context = ['errors' => $errors, 'responseBody' => $responseBody];
        $this->logger->error($message, $context);
        
        throw new AfterbuyResponseException($message);
    }
    
    
    /**
     * Collects errors from the Afterbuy response xml if available.
     *
     * @param SimpleXMLElement $xmlResponse
     *
     * @return array
     */
    private function collectErrorsFromXML(SimpleXMLElement $xmlResponse): array
    {
        $errors = [];
        if (($xmlResponseResult = $this->tryGetXmlElement($xmlResponse, 'Result'))
            && $xmlResponseErrorList = $this->tryGetXmlElement($xmlResponseResult, 'ErrorList')) {
            if (is_iterable($xmlResponseErrorList->Error)) {
                foreach ($xmlResponseErrorList->Error as $error) {
                    $errorCode   = $this->tryGetXmlString($error, 'ErrorCode');
                    $description = $this->tryGetXmlString($error, 'ErrorLongDescription');
                    
                    $errors[] = [
                        'code'        => $errorCode,
                        'description' => $description,
                    ];
                }
            }
        }
        
        return $errors;
    }
    
    
    /**
     * Tries to get a xml element's property as string.
     *
     * @param SimpleXMLElement $xml
     * @param string           $property
     *
     * @return string|null
     */
    private function tryGetXmlString(SimpleXMLElement $xml, string $property): ?string
    {
        if (property_exists($xml, $property) && method_exists($xml->$property, '__toString')) {
            return $xml->$property->__toString();
        }
        
        return null;
    }
    
    
    /**
     * Tries to get a xml element's property as xml element.
     *
     * @param SimpleXMLElement $xml
     * @param string           $property
     *
     * @return SimpleXMLElement|null
     */
    private function tryGetXmlElement(SimpleXMLElement $xml, string $property): ?SimpleXMLElement
    {
        if (property_exists($xml, $property) && $xml->$property instanceof SimpleXMLElement) {
            return $xml->$property;
        }
        
        return null;
    }
    
    
    /**
     * Checks if call status matches 'success'.
     *
     * @param string $callStatus
     *
     * @return bool
     */
    private function isSuccessful(string $callStatus): bool
    {
        return strtolower($callStatus) === strtolower(self::CALL_STATUS_SUCCESS);
    }
    
    
    /**
     * @param SimpleXMLElement $xmlResponse
     *
     * @return string
     * @throws AfterbuyResponseException
     */
    private function getCallStatus(SimpleXMLElement $xmlResponse): string
    {
        if (!property_exists($xmlResponse, 'CallStatus') || !method_exists($xmlResponse->CallStatus, '__toString')) {
            $message = "Response's 'CallStatus' could not be parsed into a string";
            throw new AfterbuyResponseException($message);
        }
        
        return $xmlResponse->CallStatus->__toString();
    }
}