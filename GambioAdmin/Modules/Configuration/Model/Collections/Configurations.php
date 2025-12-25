<?php
/* --------------------------------------------------------------
   Configurations.php 2022-08-05
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
use Gambio\Admin\Modules\Configuration\Model\Entities\Configuration;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class Configurations
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Collections
 */
class Configurations implements JsonSerializable, IteratorAggregate
{
    /**
     * @var Configuration[]
     */
    private $configurations;
    
    
    /**
     * Configurations constructor.
     *
     * @param Configuration[] $configurations
     */
    private function __construct(Configuration ...$configurations)
    {
        $this->configurations = $configurations;
    }
    
    
    /**
     * @param Configuration ...$configurations
     *
     * @return Configurations
     */
    public static function create(Configuration ...$configurations): Configurations
    {
        return new self(...$configurations);
    }
    
    
    /**
     * @return string[]
     */
    public function tags(): array
    {
        $tagIds = array_map(static function (Configuration $configuration): array {
            return $configuration->tagIds();
        },
            $this->configurations);
        
        return array_unique(array_merge([], ...$tagIds));
    }
    
    
    /**
     * @return Configuration[]
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->configurations;
    }
    
    
    /**
     * @return Traversable|Configuration[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->configurations);
    }
}