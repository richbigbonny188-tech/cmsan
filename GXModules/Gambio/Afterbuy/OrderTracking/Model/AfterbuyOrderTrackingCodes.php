<?php
/* --------------------------------------------------------------
   AfterbuyOrderTrackingCodes.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderTracking\Model;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Class AfterbuyOrderTrackingCodes
 *
 * @package GXModules\Gambio\Afterbuy\OrderTracking\Model
 */
class AfterbuyOrderTrackingCodes implements IteratorAggregate
{
    /**
     * @var AfterbuyOrderTrackingCode[]
     */
    private array $codes;
    
    
    /**
     * AfterbuyOrderTrackingCodes constructor.
     *
     * @param AfterbuyOrderTrackingCode ...$codes
     */
    public function __construct(AfterbuyOrderTrackingCode ...$codes)
    {
        $this->codes = $codes;
    }
    
    
    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->codes) === 0;
    }
    
    
    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->codes);
    }
}