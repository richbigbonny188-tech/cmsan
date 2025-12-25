<?php
/* --------------------------------------------------------------
   WithdrawalIds.php 2020-08-24
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
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use IteratorAggregate;
use Traversable;

/**
 * Class WithdrawalIds
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\Collections
 */
class WithdrawalIds implements IteratorAggregate
{
    /**
     * @var WithdrawalId[]
     */
    private $ids;
    
    
    /**
     * WithdrawalIds constructor.
     *
     * @param array $ids
     */
    private function __construct(array $ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * @param WithdrawalId ...$ids
     *
     * @return WithdrawalIds
     */
    public static function create(WithdrawalId ...$ids): WithdrawalIds
    {
        return new self($ids);
    }
    
    
    /**
     * @return Traversable|WithdrawalId[]
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
        return array_map(static function (WithdrawalId $id): int {
            return $id->value();
        },
            $this->ids);
    }
}