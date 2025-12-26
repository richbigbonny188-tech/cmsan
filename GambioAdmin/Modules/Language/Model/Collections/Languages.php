<?php
/* --------------------------------------------------------------
   Languages.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Language\Model\Language;
use IteratorAggregate;
use Traversable;

/**
 * Class Languages
 *
 * @package Gambio\Admin\Modules\Language\Model\Collections
 */
class Languages implements IteratorAggregate
{
    /**
     * @var Language[]
     */
    private $items;
    
    
    /**
     * Languages constructor.
     *
     * @param Language ...$languages
     */
    private function __construct(Language ...$languages)
    {
        $this->items = $languages;
    }
    
    
    /**
     * @param Language ...$languages
     *
     * @return Languages
     */
    public static function create(Language ...$languages): Languages
    {
        return new self(...$languages);
    }
    
    
    /**
     * @return Traversable|Language[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function (Language $language): array {
            return $language->toArray();
        },
            $this->items);
    }
}