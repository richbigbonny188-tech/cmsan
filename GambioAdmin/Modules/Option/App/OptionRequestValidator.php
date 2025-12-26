<?php
/* --------------------------------------------------------------
   OptionRequestValidator.php 2022-10-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App;

use Gambio\Core\Language\Services\LanguageService;

/**
 * Class OptionRequestValidator
 *
 * @package Gambio\Api\Modules\Option\App
 */
class OptionRequestValidator
{
    /**
     * @var LanguageService
     */
    private LanguageService $languageService;
    
    
    /**
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return string[][]
     */
    public function validateOptionPostRequestBody(array $parsedBody): array
    {
        $mandatory        = [
            'type',
            'sortOrder',
        ];
        $detailsMandatory = [
            'languageCode',
            'label',
            'adminLabel',
            'description',
        ];
        $availableLanguages = array_column($this->languageService->getAvailableAdminLanguages()->toArray(), 'code');

        $errors = $this->checkAttributes($parsedBody, $mandatory);
        foreach ($parsedBody as $index => $data) {
            if (isset($data['details']) === false) {
                $errors[$index][] = 'Attribute "details" is missing.';
                continue;
            }

            $detailsErrors = array_merge($errors[$index] ?? [],
                ...array_values($this->checkAttributes($data['details'], $detailsMandatory)),
                ...array_values($this->checkDetailLanguages($data['details'], $availableLanguages)));
            if (count($detailsErrors) > 0) {
                $errors[$index] = $detailsErrors;
            }
        }
        
        return $errors;
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return string[][]
     */
    public function validateOptionPutRequestBody(array $parsedBody): array
    {
        $mandatory        = [
            'id',
            'type',
            'sortOrder',
        ];
        $detailsMandatory = [
            'languageCode',
            'label',
            'adminLabel',
            'description',
        ];
        $availableLanguages = array_column($this->languageService->getAvailableAdminLanguages()->toArray(), 'code');
        
        $errors = $this->checkAttributes($parsedBody, $mandatory);
        foreach ($parsedBody as $index => $data) {
            if (isset($data['details']) === false) {
                $errors[$index][] = 'Attribute "details" is missing.';
                continue;
            }
            
            $detailsErrors = array_merge($errors[$index] ?? [],
                ...array_values($this->checkAttributes($data['details'], $detailsMandatory)),
                ...array_values($this->checkDetailLanguages($data['details'], $availableLanguages)));
            if (count($detailsErrors) > 0) {
                $errors[$index] = $detailsErrors;
            }
        }
        
        return $errors;
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return string[][]
     */
    public function validateOptionPatchRequestBody(array $parsedBody): array
    {
        $mandatory = [
            'id',
            'sortOrder',
        ];
        
        return $this->checkAttributes($parsedBody, $mandatory);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return string[][]
     */
    public function validateOptionValuePostRequestBody(array $parsedBody): array
    {
        $mandatory        = [
            'sortOrder',
            'image',
            'modelNumber',
            'weight',
            'price',
            'stockType',
            'stock',
            'stockCentrallyManaged',
        ];
        $detailsMandatory = [
            'languageCode',
            'label',
            'description',
        ];
        
        $errors = $this->checkAttributes($parsedBody, $mandatory);
        foreach ($parsedBody as $index => $data) {
            if (isset($data['details']) === false) {
                $errors[$index][] = 'Attribute "details" is missing.';
                continue;
            }
            
            $detailsErrors = array_merge($errors[$index] ?? [],
                                         $this->checkAttributes($data['details'], $detailsMandatory));
            if (count($detailsErrors) > 0) {
                $errors[$index] = $detailsErrors;
            }
        }
        
        return $errors;
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return string[][]
     */
    public function validateOptionValuePutRequestBody(array $parsedBody): array
    {
        $mandatory        = [
            'id',
            'sortOrder',
            'image',
            'modelNumber',
            'weight',
            'price',
            'stockType',
            'stock',
            'stockCentrallyManaged',
        ];
        $detailsMandatory = [
            'languageCode',
            'label',
            'description',
        ];
        
        $errors = $this->checkAttributes($parsedBody, $mandatory);
        foreach ($parsedBody as $index => $data) {
            if (isset($data['details']) === false) {
                $errors[$index][] = 'Attribute "details" is missing.';
                continue;
            }
            
            $detailsErrors = array_merge($errors[$index] ?? [],
                                         $this->checkAttributes($data['details'], $detailsMandatory));
            if (count($detailsErrors) > 0) {
                $errors[$index] = $detailsErrors;
            }
        }
        
        return $errors;
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return string[][]
     */
    public function validateOptionValuePatchRequestBody(array $parsedBody): array
    {
        $mandatory = [
            'id',
            'sortOrder',
        ];
        
        return $this->checkAttributes($parsedBody, $mandatory);
    }
    
    
    /**
     * @param array $parsedBody
     * @param array $mandatory
     *
     * @return array
     */
    private function checkAttributes(array $parsedBody, array $mandatory): array
    {
        $errors = [];
        
        foreach ($parsedBody as $index => $optionData) {
            $providedAttributes = $this->getArrayKeysRecursive($optionData);
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
    private function getArrayKeysRecursive(array $array, string $connectionString = '.'): array
    {
        $keys = [];
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $keys[] = $key;
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
     * @param array $availableLanguages
     *
     * @return array
     */
    public function checkDetailLanguages(array $parsedBody, array $availableLanguages): array
    {
        $errors           = [];
        $missingLanguages = array_diff($availableLanguages, array_column($parsedBody, 'languageCode'));
        
        foreach ($missingLanguages as $index => $missingLanguage) {
            $errors[$index][] = 'The language "' . $missingLanguage . '" in the "details" is missing.';
        }
        
        return $errors;
    }
}