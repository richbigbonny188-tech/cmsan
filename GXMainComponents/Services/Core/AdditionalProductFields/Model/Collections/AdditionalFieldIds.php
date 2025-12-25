<?php
/*--------------------------------------------------------------
   AdditionalFieldIds.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections;

use ArrayIterator;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use IteratorAggregate;
use MainFactory;
use Traversable;

/**
 * Class AdditionalFieldIds
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections
 */
class AdditionalFieldIds implements IteratorAggregate
{
    /**
     * @var AdditionalFieldId[]
     */
    protected $ids;
    
    
    /**
     * AdditionalFieldIds constructor.
     *
     * @param AdditionalFieldId[] $ids
     */
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * @param AdditionalFieldId ...$ids
     *
     * @return AdditionalFieldIds
     */
    public static function create(AdditionalFieldId ...$ids): AdditionalFieldIds
    {
        return MainFactory::create(AdditionalFieldIds::class, $ids);
    }
    
    
    /**
     * @return Traversable|AdditionalFieldId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function(AdditionalFieldId $id): int {
            
            return $id->value();
        }, $this->ids);
    }
}