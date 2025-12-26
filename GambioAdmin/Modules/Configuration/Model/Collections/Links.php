<?php
/* --------------------------------------------------------------
   Links.php 2022-08-05
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
use Gambio\Admin\Modules\Configuration\Model\ValueObjects\Link;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class Links
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Collections
 */
class Links implements JsonSerializable, IteratorAggregate
{
    /**
     * @var Link[]
     */
    private $links;
    
    
    /**
     * Links constructor.
     *
     * @param Link[] $links
     */
    private function __construct(Link ...$links)
    {
        $this->links = $links;
    }
    
    
    /**
     * @param Link ...$links
     *
     * @return Links
     */
    public static function create(Link ...$links): Links
    {
        return new self(...$links);
    }
    
    
    /**
     * @return Link[]
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->links;
    }
    
    
    /**
     * @return Traversable|Link[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->links);
    }
}