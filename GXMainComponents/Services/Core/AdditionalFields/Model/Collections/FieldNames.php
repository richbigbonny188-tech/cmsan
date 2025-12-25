<?php
/*--------------------------------------------------------------
   FieldNames.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections;

use ArrayIterator;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldName;
use IteratorAggregate;
use MainFactory;
use Traversable;

/**
 * Class FieldNames
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections
 */
class FieldNames implements IteratorAggregate
{
    /**
     * @var FieldName[]
     */
    protected $fieldNames = [];
    
    
    /**
     * FieldNames constructor.
     *
     * @param FieldName[] $fieldNames
     */
    public function __construct(array $fieldNames)
    {
        foreach ($fieldNames as $fieldName) {
            
            $this->fieldNames[$fieldName->languageCode()] = $fieldName;
        }
    }
    
    
    /**
     * @param FieldName ...$fieldNames
     *
     * @return FieldNames
     */
    public static function create(FieldName ...$fieldNames): FieldNames
    {
        return MainFactory::create(FieldNames::class, $fieldNames);
    }
    
    
    /**
     * @param FieldName $newName
     *
     * @return FieldNames
     */
    public function with(FieldName $newName): FieldNames
    {
        $items = $this->fieldNames;
        
        $items[$newName->languageCode()] = $newName;
        
        return MainFactory::create(FieldNames::class, $items);
    }
    
    /**
     * @return Traversable|FieldName[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->fieldNames);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        
        foreach ($this as $fieldName) {
    
            $languageCode = strtolower($fieldName->languageCode());
            $name         = $fieldName->name();
            
            $result[$languageCode] = $name;
        }
        
        return $result;
    }
}