<?php
/* --------------------------------------------------------------
   TrackingCodeIds.php 2020-09-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use IteratorAggregate;
use Traversable;

/**
 * Class TrackingCodeIds
 *
 * @package Gambio\Admin\Modules\TrackingCode\Model\Collections
 */
class TrackingCodeIds implements IteratorAggregate
{
    /**
     * @var TrackingCodeId[]
     */
    private $ids;
    
    
    /**
     * TrackingCodeIds constructor.
     *
     * @param array $ids
     */
    private function __construct(array $ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * @param TrackingCodeId ...$ids
     *
     * @return TrackingCodeIds
     */
    public static function create(TrackingCodeId ...$ids): TrackingCodeIds
    {
        return new self($ids);
    }
    
    
    /**
     * @return Traversable|TrackingCodeId[]
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
        return array_map(static function (TrackingCodeId $id): int {
            return $id->value();
        },
            $this->ids);
    }
}