<?php
/*--------------------------------------------------------------
   AdditionalFields.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections;

use ArrayIterator;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use IteratorAggregate;
use MainFactory;
use Traversable;

/**
 * Class AdditionalFields
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections
 */
class AdditionalFields implements IteratorAggregate
{
    /**
     * @var AdditionalField[]
     */
    protected $additionalFields;
    
    
    /**
     * AdditionalFields constructor.
     *
     * @param AdditionalField[] $additionalFields
     */
    public function __construct(array $additionalFields)
    {
        $this->additionalFields = $additionalFields;
    }
    
    
    /**
     * @param AdditionalField ...$fieldNames
     *
     * @return AdditionalFields
     */
    public static function create(AdditionalField ...$fieldNames): AdditionalFields
    {
        return MainFactory::create(AdditionalFields::class, $fieldNames);
    }
    
    
    /**
     * @return Traversable|AdditionalField[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->additionalFields);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (AdditionalField $additionalField): array {
            
            return $additionalField->toArray();
        },
            $this->additionalFields);
    }
}