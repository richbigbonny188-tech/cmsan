<?php
/*--------------------------------------------------------------
   AdditionalField.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Model;

use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldNames;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldName;
use MainFactory;

/**
 * Class AdditionalField
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Model
 */
class AdditionalField
{
    /**
     * @var FieldId
     */
    protected $id;
    
    /**
     * @var FieldNames
     */
    protected $fieldNames;
    
    
    /**
     * AdditionalField constructor.
     *
     * @param FieldId    $id
     * @param FieldNames $fieldNames
     */
    public function __construct(FieldId $id, FieldNames $fieldNames)
    {
        $this->id         = $id;
        $this->fieldNames = $fieldNames;
    }
    
    
    /**
     * @param FieldId    $id
     * @param FieldNames $fieldNames
     *
     * @return AdditionalField
     */
    public static function create(FieldId $id, FieldNames $fieldNames): AdditionalField
    {
        return MainFactory::create(AdditionalField::class, $id, $fieldNames);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return FieldNames
     */
    public function fieldNames(): FieldNames
    {
        return $this->fieldNames;
    }
    
    
    /**
     * @param FieldName $fieldName
     */
    public function changeFieldName(FieldName $fieldName): void
    {
        $this->fieldNames = $this->fieldNames()->with($fieldName);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'    => $this->id(),
            'names' => $this->fieldNames()->toArray(),
        ];
    }
}