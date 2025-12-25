<?php
/* --------------------------------------------------------------
   TrackingCodes.php 2020-09-01
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
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use IteratorAggregate;
use Traversable;

class TrackingCodes implements IteratorAggregate
{
    /**
     * @var TrackingCode[]
     */
    private $trackingCodes;
    
    
    /**
     * TrackingCodes constructor.
     *
     * @param array $trackingCodes
     */
    private function __construct(array $trackingCodes)
    {
        $this->trackingCodes = $trackingCodes;
    }
    
    
    /**
     * @param TrackingCode ...$trackingCodes
     *
     * @return TrackingCodes
     */
    public static function create(TrackingCode ...$trackingCodes): TrackingCodes
    {
        return new self($trackingCodes);
    }
    
    
    /**
     * @return Traversable|TrackingCode[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->trackingCodes);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (TrackingCode $trackingCode): array {
            return $trackingCode->toArray();
        },
            $this->trackingCodes);
    }
}