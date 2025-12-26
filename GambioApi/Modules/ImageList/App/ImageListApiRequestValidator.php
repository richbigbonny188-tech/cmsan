<?php
/*--------------------------------------------------------------
   ImageListApiRequestValidator.php 2021-06-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App;

/**
 * Class ImageListApiRequestValidator
 * @package Gambio\Api\Modules\ImageList\App
 */
class ImageListApiRequestValidator
{
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateUpdateImageListRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['id', 'name',]);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateCreateImageListRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['name',]);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateUpdateSortOrderRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['relativePath', 'sortOrder',]);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateUpdateImageRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['relativePath', 'sortOrder', 'titles', 'altTitles']);
    }
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateAddImageRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['relativePath', 'sortOrder', 'titles', 'altTitles']);
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