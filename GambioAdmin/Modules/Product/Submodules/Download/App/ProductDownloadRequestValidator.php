<?php
/*--------------------------------------------------------------
   ProductDownloadRequestValidator.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

/**
 * Class ProductDownloadRequestValidator
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App;
 */
class ProductDownloadRequestValidator
{
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
    public function validateUpdateBody(array $parsedBody): array
    {
        $mandatoryVariantFields = [
            "id",
            "imageListId",
            "modelNumber",
            "weight",
            "price",
            "stockType",
            "stock",
            "sortOrder",
        ];
    
        return $this->checkAttributes($parsedBody, $mandatoryVariantFields);
    }
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateCreationBody(array $parsedBody): array
    {
        $mandatoryVariantFields = [
            "optionId",
            "optionValueId",
            "imageListId",
            "modelNumber",
            "weight",
            "price",
            "stockType",
            "stock",
            "sortOrder",
        ];
    
        return $this->checkAttributes($parsedBody, $mandatoryVariantFields);
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