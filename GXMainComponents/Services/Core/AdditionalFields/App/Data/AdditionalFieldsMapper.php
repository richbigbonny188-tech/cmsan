<?php
/*--------------------------------------------------------------
   AdditionalFieldsMapper.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\App\Data;

use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\AdditionalFields;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldFactory;

/**
 * Class AdditionalFieldsMapper
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\App\Data
 */
class AdditionalFieldsMapper extends AdditionalFieldFactory
{
    protected const SORT_BY_COLUMNS = [
        'id' => 'af.additional_field_id',
    ];
    
    /**
     * @param int   $id
     * @param array $fieldNames
     *
     * @return AdditionalField
     */
    public function mapAdditionalField(int $id, array $fieldNames): AdditionalField
    {
        $result = [];
        
        foreach ($fieldNames as $languageCode => $fieldName) {
    
            $result[] = $this->createFieldName($languageCode, $fieldName);
        }
        
        $fieldNames = $this->createFieldNames(...$result);
        
        return AdditionalField::create($this->createFieldId($id), $fieldNames);
    }
    
    
    /**
     * @param array $additionalFields
     *
     * @return AdditionalFields
     */
    public function mapAdditionalFields(array $additionalFields): AdditionalFields
    {
        $result = [];
        
        foreach ($additionalFields as $fieldId => $fieldNames) {
            
            $result[] = $this->mapAdditionalField($fieldId, $fieldNames);
        }
        
        return AdditionalFields::create(...$result);
    }
    
    
    /**
     * @param string $sortByColumns
     *
     * @return array
     */
    public function mapSortByFields(string $sortByColumns): array
    {
        foreach (explode(',', $sortByColumns) as $column) {
            
            $prefix = $this->string_shift($column);
            
            if (isset(static::SORT_BY_COLUMNS[$column])) {
    
                $result[] = [static::SORT_BY_COLUMNS[$column] , $prefix === '+' ? 'ASC' : 'DESC'];
            }
        }
        
        return $result ?? [];
    }
    
    
    /**
     * @param string $string
     *
     * @return string
     */
    protected function string_shift(string &$string): string
    {
        $part1 = substr($string, 0, 1);
        $part2 = substr($string, 1);
        
        $string = $part2;
        
        return $part1;
    }
}