<?php
/* --------------------------------------------------------------
   LanguageIds.php 2020-10-19
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
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageId;
use IteratorAggregate;
use Traversable;

/**
 * Class LanguageIds
 *
 * @package Gambio\Admin\Modules\Language\Model\Collections
 */
class LanguageIds implements IteratorAggregate
{
    /**
     * @var LanguageId[]
     */
    private $ids;
    
    
    /**
     * LanguageIds constructor.
     *
     * @param array $ids
     */
    private function __construct(array $ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * @param LanguageId ...$ids
     *
     * @return LanguageIds
     */
    public static function create(LanguageId ...$ids): LanguageIds
    {
        return new self($ids);
    }
    
    
    /**
     * @return Traversable|LanguageId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (LanguageId $id): int {
            return $id->value();
        },
            $this->ids);
    }
}