<?php
/* --------------------------------------------------------------
   LanguageFileRegistry.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\Model\Collections;

use ArrayIterator;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\LanguageFile;
use IteratorAggregate;
use Traversable;

/**
 * Class LanguageFileRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
class LanguageFileRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var LanguageFile[]
     */
    private $components;
    
    
    /**
     * LanguageFileRegistry constructor.
     *
     * @param LanguageFile ...$components
     */
    private function __construct(LanguageFile ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param LanguageFile ...$components
     *
     * @return LanguageFileRegistry
     */
    public static function create(LanguageFile ...$components): LanguageFileRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return LanguageFile[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|LanguageFile[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
}