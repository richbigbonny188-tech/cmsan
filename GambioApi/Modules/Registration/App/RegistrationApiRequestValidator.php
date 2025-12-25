<?php
/*--------------------------------------------------------------
   CustomerApiRequestValidator.php 2022-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Registration\App;

/**
 * Class RegistrationApiRequestValidator
 *
 * @package Gambio\Api\Modules\Registration\App
 */
class RegistrationApiRequestValidator
{
    private static array $customerFields = [
        'isFavorite',
        'credit',
    ];
    
    private static array $personalInformationFields = [
        'personalInformation.gender',
        'personalInformation.firstName',
        'personalInformation.lastName',
        'personalInformation.dateOfBirth',
        'personalInformation.customerNumber',
    ];
    
    private static array $contactInformationFields = [
        'contactInformation.email',
        'contactInformation.phoneNumber',
        'contactInformation.faxNumber',
    ];
    
    private static array $businessInformationFields = [
        'businessInformation.companyName',
        'businessInformation.vatId',
        'businessInformation.isTradesperson',
    ];
    
    private static array $locationInformationFields = [
        "locationInformation.houseNumber",
        "locationInformation.postcode",
        "locationInformation.city",
        "locationInformation.country.name",
        "locationInformation.country.isoCode2",
        "locationInformation.additionalInformation",
        "locationInformation.suburb",
        "locationInformation.state",
    ];
    
    
    /**
     * @param array $parsedBody
     * @param array $mandatory
     *
     * @return array
     */
    protected function checkAttributes(
        array $parsedBody,
        array $mandatory
    ): array {
        $errors = [];
        
        foreach ($parsedBody as $index => $details) {
            
            $providedAttributes = $this->getArrayKeysRecursive($details);
            $missingAttributes  = array_diff($mandatory, $providedAttributes);
            
            if (count($missingAttributes) > 0) {
                
                $errors[$index] = [];
                foreach ($missingAttributes as $missingAttribute) {
                    
                    $errors[$index][] = 'Attribute "' . $missingAttribute . '" is missing.';
                }
            }
        }
        
        return $errors;
    }
    
    
    /**
     * @param array  $array
     * @param string $connectionString
     *
     * @return array
     */
    protected function getArrayKeysRecursive(
        array  $array,
        string $connectionString = '.'
    ): array {
        
        $keys = [];
        foreach ($array as $key => $value) {
            
            $keys[] = $key;
            
            if (!is_array($value)) {
                
                continue;
            }
            
            foreach ($this->getArrayKeysRecursive($value) as $subKey) {
                
                $keys[] = $key . $connectionString . $subKey;
            }
        }
        
        return $keys;
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateRegistrationRequest(array $parsedBody): array
    {
        $mandatoryFields = [
            ...static::$customerFields,
            ...static::$personalInformationFields,
            ...static::$contactInformationFields,
            ...static::$businessInformationFields,
            ...static::$locationInformationFields,
        ];
        
        return $this->checkAttributes($parsedBody, $mandatoryFields);
    }
}