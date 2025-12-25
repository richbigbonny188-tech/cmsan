<?php
/*--------------------------------------------------------------------
 AdditionalOptions.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use IteratorAggregate;
use Traversable;

/**
 * Class AdditionalOptions
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections
 */
class AdditionalOptions implements IteratorAggregate
{
    /**
     * AdditionalOptions constructor.
     *
     * @param AdditionalOption[] $additionalOptions
     */
    private function __construct(private array $additionalOptions) { }
    
    
    /**
     * @param AdditionalOption ...$additionalOptions
     *
     * @return AdditionalOptions
     */
    public static function create(AdditionalOption ...$additionalOptions): AdditionalOptions
    {
        return new self($additionalOptions);
    }
    
    
    /**
     * @return Traversable|AdditionalOption[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->additionalOptions);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $callback = static fn(AdditionalOption $option): array => $option->toArray();
        
        return array_map($callback, $this->additionalOptions);
    }
}