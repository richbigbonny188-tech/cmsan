<?php
/* --------------------------------------------------------------
   TemplateRegistry.php 2021-05-14
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
use Gambio\Core\GXModules\Model\ValueObjects\Template;
use IteratorAggregate;
use Traversable;

/**
 * Class TemplateRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
class TemplateRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var Template[]
     */
    private $components;
    
    /**
     * @var string[]
     */
    private $primitivArray;
    
    
    /**
     * TemplateRegistry constructor.
     *
     * @param Template ...$components
     */
    private function __construct(Template ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param Template ...$components
     *
     * @return TemplateRegistry
     */
    public static function create(Template ...$components): TemplateRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return Template[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|Template[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
    
    
    /**
     * @return string[]
     */
    public function toArray(): array
    {
        if ($this->primitivArray === null) {
            $templatePaths = [];
            foreach ($this->components as $template) {
                $templatePaths[] = $template->filePath();
            }
            $this->primitivArray = $templatePaths;
        }
        
        return $this->primitivArray;
    }
}