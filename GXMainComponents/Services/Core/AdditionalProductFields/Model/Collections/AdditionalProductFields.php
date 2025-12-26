<?php
/*--------------------------------------------------------------
   AdditionalProductFields.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections;

use ArrayIterator;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use InvalidArgumentException;
use IteratorAggregate;
use MainFactory;
use Traversable;

/**
 * Class AdditionalProductFields
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections
 */
class AdditionalProductFields implements IteratorAggregate
{
    /**
     * @var AdditionalProductField[]
     */
    protected $productFields = [];
    
    
    /**
     * AdditionalProductFields constructor.
     *
     * @param AdditionalProductField[] $productFields
     */
    public function __construct(array $productFields)
    {
        foreach ($productFields as $productField) {
            
            $this->productFields[$productField->id()] = $productField;
        }
    }
    
    
    /**
     * @param AdditionalProductField ...$productFields
     *
     * @return AdditionalProductFields
     */
    public static function create(AdditionalProductField ...$productFields): AdditionalProductFields
    {
        return MainFactory::create(AdditionalProductFields::class, $productFields);
    }
    
    
    /**
     * @return AdditionalProductFields
     */
    public static function createAsEmpty(): AdditionalProductFields
    {
        return MainFactory::create(AdditionalProductFields::class, []);
    }
    
    
    /**
     * @return Traversable|AdditionalProductField[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->productFields);
    }
    
    
    /**
     * @param AdditionalFieldId $fieldId
     *
     * @return AdditionalProductField
     */
    public function getByFieldId(AdditionalFieldId $fieldId): AdditionalProductField
    {
        if (isset($this->productFields[$fieldId->value()])) {
            
            return $this->productFields[$fieldId->value()];
        }
        
        throw new InvalidArgumentException(sprintf('Additional product field with the id "%s" does not exist', $fieldId->value()));
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = array_map(static function(AdditionalProductField $productField): array {
    
            return $productField->toArray();
        }, $this->productFields);
        
        return array_values($array);
    }
}