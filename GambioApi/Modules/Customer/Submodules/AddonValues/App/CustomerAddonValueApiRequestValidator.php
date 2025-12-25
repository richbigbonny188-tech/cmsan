<?php
/*--------------------------------------------------------------
   CustomerAddonValueApiRequestValidator.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App;

/**
 * Class CustomerAddonValueApiRequestValidator
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues\App
 */
class CustomerAddonValueApiRequestValidator
{
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateCreateRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['key', 'value']);
    }
    
    
    /**
     * @param array $parsedBody
     *
     * @return array
     */
    public function validateUpdateRequest(array $parsedBody): array
    {
        return $this->checkAttributes($parsedBody, ['key', 'value']);
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
}