<?php
/*--------------------------------------------------------------
   ProductVariantApiRequestValidator.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App;

/**
 * Class ProductVariantApiRequestValidator
 * @package Gambio\Api\Modules\ProductVariant\App
 */
class ProductVariantApiRequestValidator
{
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateCreationBody(array $parsedBody): array
    {
        $mandatoryVariantFields = [
            'sortOrder',
            'combination',
            'EAN',
            'GTIN',
            'ASIN',
            'stock',
            'weight',
            'priceType',
            'weightType',
            'price',
            'vpeScalarValue',
            'vpeUnitId',
            'deliveryTimeId',
            'imageListId',
        ];
    
        $errors = $this->checkAttributes($parsedBody, $mandatoryVariantFields);
    
        foreach ($parsedBody as $index => $item) {
        
            foreach ($this->validateGenerationBody($item['combination']) as $valueErrors) {
            
                foreach ($valueErrors as $error) {
                
                    $errors[$index][] = $error;
                }
            }
        }
    
        return $errors;
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateUpdateBody(array $parsedBody): array
    {
        $mandatoryVariantFields = [
            'sortOrder',
            'id',
            'modelNumber',
            'EAN',
            'GTIN',
            'ASIN',
            'stock',
            'weight',
            'priceType',
            'weightType',
            'price',
            'vpeScalarValue',
            'vpeUnitId',
            'deliveryTimeId',
            'imageListId',
        ];
    
        return $this->checkAttributes($parsedBody, $mandatoryVariantFields);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateUpdateSortOrderRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['id', 'sortOrder',]);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateGenerationBody(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['optionId', 'optionValueId',]);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateOptionRemovalBody(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['optionId', 'retainableOptionValueId']);
    }
    
    
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
        array $array,
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
}