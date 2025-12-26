<?php
/* --------------------------------------------------------------
   ResponseDataTrimmerTrait.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Responses;

/**
 * Trait ResponseDataTrimmerTrait
 *
 * @package Gambio\Api\Application\Responses
 */
trait ResponseDataTrimmerTrait
{
    /**
     * @param array $collectionData
     * @param array $wantedFields
     *
     * @return array
     */
    public function trimCollectionData(array $collectionData, array $wantedFields): array
    {
        foreach ($collectionData as &$document) {
            $this->trimDocumentData($document, $wantedFields);
        }
        
        return $collectionData;
    }
    
    
    /**
     * @param array  $document
     * @param array  $wantedFields
     * @param string $indexBase
     */
    private function trimDocumentData(array &$document, array $wantedFields, string $indexBase = ''): void
    {
        foreach ($document as $index => $value) {
            $matches = preg_grep('/^' . $indexBase . $index . '(\..+)?$/i', $wantedFields);
            
            if (count($matches) === 0) {
                unset($document[$index]);
            } elseif (is_array($value)) {
                if (array_keys($value) === range(0, count($value) - 1)) {
                    foreach ($value as &$subValue) {
                        $this->trimDocumentData($subValue, $wantedFields, $index . '.');
                    }
                    unset($subValue);
                    continue;
                }
                
                $this->trimDocumentData($document[$index], $wantedFields, $index . '.');
            }
        }
    }
}