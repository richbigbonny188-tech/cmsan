<?php
/*--------------------------------------------------------------
   SelectedOptions.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\SelectedOption;
use IteratorAggregate;

class SelectedOptions implements IteratorAggregate
{
    /**
     * @var SelectedOption[]
     */
    private array $values;
    
    
    /**
     * Constructor
     */
    private function __construct(array $options)
    {
        $this->values = $options;
    }
    
    
    /**
     * Returns a new instance of Selected Options
     */
    public static function create(SelectedOption ...$options): self
    {
        return new self($options);
    }
    
    
    /**
     * @return ArrayIterator|SelectedOption[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
    
    
    /**
     * Returns an array containing the Selected Options
     */
    public function toArray(): array
    {
        return array_map(fn($value): array => $value->toArray(), $this->values);
    }
}
