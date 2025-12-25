<?php
/*--------------------------------------------------------------
   FieldIds.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections;

use ArrayIterator;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use IteratorAggregate;
use MainFactory;
use Traversable;

/**
 * Class FieldIds
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections
 */
class FieldIds implements IteratorAggregate
{
    /**
     * @var FieldId[]
     */
    protected $fieldIds;
    
    
    /**
     * FieldIds constructor.
     *
     * @param FieldId[] $fieldIds
     */
    public function __construct(array $fieldIds)
    {
        $this->fieldIds = $fieldIds;
    }
    
    
    /**
     * @param FieldId ...$fieldIds
     *
     * @return FieldIds
     */
    public static function create(FieldId ...$fieldIds): FieldIds
    {
        return MainFactory::create(FieldIds::class, $fieldIds);
    }
    
    
    /**
     * @return Traversable|FieldId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->fieldIds);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (FieldId $fieldId): int {
            
            return $fieldId->value();
        },
            $this->fieldIds);
    }
}