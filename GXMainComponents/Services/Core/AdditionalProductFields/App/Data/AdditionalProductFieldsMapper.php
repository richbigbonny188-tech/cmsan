<?php
/*--------------------------------------------------------------
   AdditionalProductFieldsMapper.php 2021-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalFieldValues;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalProductFields;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\ProductId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldFactory;

/**
 * Class AdditionalProductFieldsMapper
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data
 */
class AdditionalProductFieldsMapper extends AdditionalProductFieldFactory
{
    /**
     * @param AdditionalFieldId     $id
     * @param ProductId             $productId
     * @param AdditionalFieldValues $values
     *
     * @return AdditionalProductField
     */
    public function createAdditionalProductField(
        AdditionalFieldId $id,
        ProductId $productId,
        AdditionalFieldValues $values
    ): AdditionalProductField {
        
        return AdditionalProductField::create($id, $productId, $values);
    }
    
    
    /**
     * @param AdditionalProductField ...$additionalProductFields
     *
     * @return AdditionalProductFields
     */
    public function createAdditionalProductFields(
        AdditionalProductField ...$additionalProductFields
    ): AdditionalProductFields {
        
        return AdditionalProductFields::create(...$additionalProductFields);
    }
    
    
    /**
     * @return AdditionalProductFields
     */
    public function createEmptyAdditionalProductFields(): AdditionalProductFields
    {
        return AdditionalProductFields::createAsEmpty();
    }
    
    
    /**
     * @param ProductId $productId
     * @param array     $readerData
     *
     * @return AdditionalProductFields
     */
    public function mapAdditionalFields(ProductId $productId, array $readerData): AdditionalProductFields
    {
        $fields = [];
    
        foreach ($readerData as $fieldId => $localizedContent) {
        
            $values = [];
        
            foreach ($localizedContent as $languageCode => $value) {
            
                $values[] = $this->createAdditionalFieldValue($languageCode, $value);
            }
        
            $fields[] = $this->createAdditionalProductField($this->createAdditionalFieldId($fieldId),
                                                            $productId,
                                                            $this->createAdditionalFieldValues(...$values));
        }
        
        return count($fields) ? $this->createAdditionalProductFields(...$fields) : $this->createEmptyAdditionalProductFields();
    }
}