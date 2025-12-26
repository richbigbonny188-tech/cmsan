<?php
/*--------------------------------------------------------------
   AdditionalFieldValues.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections;

use ArrayIterator;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldValue;
use IteratorAggregate;
use MainFactory;
use Traversable;

/**
 * Class AdditionalFieldValues
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections
 */
class AdditionalFieldValues implements IteratorAggregate
{
    /**
     * @var AdditionalFieldValue[]
     */
    protected $values = [];
    
    
    /**
     * AdditionalFieldValues constructor.
     *
     * @param AdditionalFieldValue[] $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $value) {
            
            $this->values[$value->languageCode()] = $value;
        }
    }
    
    
    /**
     * @param AdditionalFieldValue ...$values
     *
     * @return AdditionalFieldValues
     */
    public static function create(AdditionalFieldValue ...$values): AdditionalFieldValues
    {
        return MainFactory::create(AdditionalFieldValues::class, $values);
    }
    
    
    /**
     * @return Traversable|AdditionalFieldValue[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }
    
    
    /**
     * @param AdditionalFieldValue ...$values
     *
     * @return AdditionalFieldValues
     */
    public function with(AdditionalFieldValue ...$values): AdditionalFieldValues
    {
        $items = $this->values;
    
        foreach ($values as $value) {
            
            $items[$value->languageCode()] = $value;
        }
        
        return MainFactory::create(AdditionalFieldValues::class, $items);
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
    
        foreach ($this as $value) {
    
            $languageCode = strtolower($value->languageCode());
            $value        = $value->value();
    
            $result[$languageCode] = $value;
        }
        
        return $result;
    }
    
    
    /**
     * @return array
     */
    public function languageCodes(): array
    {
        $values = array_map(static function (AdditionalFieldValue $value): string {
        
            return $value->languageCode();
        },
            $this->values);
    
        return array_values(array_unique($values));
    }
}