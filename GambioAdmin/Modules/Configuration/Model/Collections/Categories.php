<?php
/* --------------------------------------------------------------
   Categories.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Configuration\Model\Entities\Category;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class Categories
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Collections
 */
class Categories implements JsonSerializable, IteratorAggregate
{
    /**
     * @var Category[]
     */
    private $categories;
    
    
    /**
     * Categories constructor.
     *
     * @param Category[] $categories
     */
    private function __construct(Category ...$categories)
    {
        $this->categories = $categories;
    }
    
    
    /**
     * @param Category ...$categories
     *
     * @return Categories
     */
    public static function create(Category ...$categories): Categories
    {
        return new self(...$categories);
    }
    
    
    /**
     * @return array Returns array of objects, that matches the Category schema from "configuration.schema.json".
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->categories;
    }
    
    
    /**
     * @return Traversable|Category[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->categories);
    }
}