<?php
/* --------------------------------------------------------------
   Withdrawals.php 2020-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use IteratorAggregate;
use Traversable;

/**
 * Class Withdrawals
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\Collections
 */
class Withdrawals implements IteratorAggregate
{
    /**
     * @var Withdrawal[]
     */
    private $withdrawals;
    
    
    /**
     * Withdrawals constructor.
     *
     * @param array $withdrawals
     */
    private function __construct(array $withdrawals)
    {
        $this->withdrawals = $withdrawals;
    }
    
    
    /**
     * @param Withdrawal ...$withdrawals
     *
     * @return Withdrawals
     */
    public static function create(Withdrawal ...$withdrawals): Withdrawals
    {
        return new self($withdrawals);
    }
    
    
    /**
     * @return Traversable|Withdrawal[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->withdrawals);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (Withdrawal $withdrawal): array {
            return $withdrawal->toArray();
        },
            $this->withdrawals);
    }
}