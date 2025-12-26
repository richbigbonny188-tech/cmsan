<?php
/* --------------------------------------------------------------
   OrderIds.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Model;

use ArrayIterator;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use IteratorAggregate;
use Traversable;

/**
 * Class OrderIds
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model
 */
class OrderIds implements IteratorAggregate
{
    /**
     * @var OrderId[]
     */
    private array $orderIds;
    
    
    /**
     * OrderIds constructor.
     *
     * @param OrderId ...$orderIds
     */
    public function __construct(OrderId ...$orderIds)
    {
        $this->orderIds = $orderIds;
    }
    
    
    /**
     * @return string
     */
    public function orderIdsWithComma(): string
    {
        $cb  = fn(OrderId $orderId): string => $orderId->asString();
        $ids = array_map($cb, $this->orderIds);
        
        return implode(',', $ids);
    }
    
    
    /**
     * Creates a 16chars long hash value representing the ids.
     *
     * @return string
     */
    public function hash(): string
    {
        $withComma = $this->orderIdsWithComma();
        
        return substr(hash('sha256', $withComma), 0, 16);
    }
    
    
    /**
     * @inheritDoc
     * @return OrderId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->orderIds);
    }
}