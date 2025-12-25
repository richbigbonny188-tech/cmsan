<?php
/*--------------------------------------------------------------
   PaymentMethods.php 2022-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\PaymentModule\Model\PaymentMethod;
use IteratorAggregate;
use Traversable;

/**
 * Class PaymentMethods
 *
 * @package Gambio\Admin\Modules\PaymentModule\Model\Collections
 */
class PaymentMethods implements IteratorAggregate
{
    /**
     * @var PaymentMethod[]
     */
    private array $paymentMethods;
    
    
    /**
     * PaymentMethods constructor.
     *
     * @param PaymentMethod[] $paymentMethods
     */
    private function __construct(array $paymentMethods)
    {
        $this->paymentMethods = $paymentMethods;
    }
    
    
    /**
     * @param PaymentMethod ...$paymentMethods
     *
     * @return PaymentMethods
     */
    public static function create(PaymentMethod ...$paymentMethods): PaymentMethods
    {
        return new self($paymentMethods);
    }
    
    
    /**
     * @return Traversable|PaymentMethod[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->paymentMethods);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(PaymentMethod $method): array => $method->toArray(), $this->paymentMethods);
    }
}