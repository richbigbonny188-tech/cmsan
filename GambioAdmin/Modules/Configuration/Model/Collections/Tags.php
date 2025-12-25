<?php
/* --------------------------------------------------------------
   Tags.php 2022-08-05
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
use Gambio\Admin\Modules\Configuration\Model\Entities\Tag;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class Tags
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Collections
 */
class Tags implements JsonSerializable, IteratorAggregate
{
    /**
     * @var Tag[]
     */
    private $tags;
    
    
    /**
     * Tags constructor.
     *
     * @param Tag[] $tags
     */
    private function __construct(Tag ...$tags)
    {
        $this->tags = $tags;
    }
    
    
    /**
     * @param Tag ...$tags
     *
     * @return Tags
     */
    public static function create(Tag ...$tags): Tags
    {
        return new self(...$tags);
    }
    
    
    /**
     * @return array Returns array of objects, that matches the Tag schema from "configuration.schema.json".
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->tags;
    }
    
    
    /**
     * @return Traversable|Tag[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tags);
    }
}