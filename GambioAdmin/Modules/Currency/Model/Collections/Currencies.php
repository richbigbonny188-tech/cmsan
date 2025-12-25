<?php
/*--------------------------------------------------------------
   Currencies.php 2022-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Currency\Model\Currency;
use IteratorAggregate;
use Traversable;

/**
 * Class Currencies
 *
 * @package Gambio\Admin\Modules\Currency\Model\Collections
 */
class Currencies implements IteratorAggregate
{
    /**
     * @var Currency[]
     */
    private $currencies;
    
    
    /**
     * Currencies constructor.
     *
     * @param Currency[] $currencies
     */
    private function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }
    
    
    /**
     * @param Currency ...$currencies
     *
     * @return Currencies
     */
    public static function create(Currency ...$currencies): Currencies
    {
        return new self($currencies);
    }
    
    
    /**
     * @return Traversable|Currency[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->currencies);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (Currency $option): array {
            return $option->toArray();
        },
            $this->currencies);
    }
}