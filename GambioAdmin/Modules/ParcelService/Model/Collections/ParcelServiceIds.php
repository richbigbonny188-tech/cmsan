<?php
/* --------------------------------------------------------------
   ParcelServiceIds.php 2020-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use IteratorAggregate;
use Traversable;

/**
 * Class ParcelServiceIds
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Collections
 */
class ParcelServiceIds implements IteratorAggregate
{
    /**
     * @var ParcelServiceId[]
     */
    private $ids;
    
    
    /**
     * ParcelServiceIds constructor.
     *
     * @param array $ids
     */
    private function __construct(array $ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * @param ParcelServiceId ...$ids
     *
     * @return ParcelServiceIds
     */
    public static function create(ParcelServiceId ...$ids): ParcelServiceIds
    {
        return new self($ids);
    }
    
    
    /**
     * @return Traversable|ParcelServiceId[]
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
        return array_map(static function (ParcelServiceId $id): int {
            return $id->value();
        },
            $this->ids);
    }
}