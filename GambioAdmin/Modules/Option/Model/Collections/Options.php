<?php
/* --------------------------------------------------------------
   Options.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Option\Model\Option;
use IteratorAggregate;
use Traversable;

/**
 * Class Options
 *
 * @package Gambio\Admin\Modules\Option\Model\Collections
 * @codeCoverageIgnore
 */
class Options implements IteratorAggregate
{
    /**
     * @var Option[]
     */
    private $options;
    
    
    /**
     * Options constructor.
     *
     * @param Option[] $options
     */
    private function __construct(array $options)
    {
        $this->options = $options;
    }
    
    
    /**
     * @param Option ...$options
     *
     * @return Options
     */
    public static function create(Option ...$options): Options
    {
        return new self($options);
    }
    
    
    /**
     * @return Traversable|Option[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->options);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (Option $option): array {
            return $option->toArray();
        },
            $this->options);
    }
}