<?php
/* --------------------------------------------------------------
   GX4ModuleProcessor.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\GXModules\Model\Collections\GX4ModuleRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\GX4Module;
use SplFileInfo;

/**
 * Class GX4ModuleProcessor
 *
 * @package Gambio\Core\GXModules\App\Data\Processors
 */
class GX4ModuleProcessor implements GXModuleComponentProcessor
{
    use DeterminesFqn;
    
    /**
     * @var GX4Module[]
     */
    private $components;
    
    
    /**
     * GX4ModuleProcessor constructor.
     *
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
    }
    
    
    /**
     * @inheritDoc
     */
    public function resetInternalState(): void
    {
        $this->components = [];
    }
    
    
    /**
     * @inheritDoc
     */
    public function processModuleFile(SplFileInfo $moduleFile): void
    {
        if (substr($moduleFile->getFilename(), -10) !== 'Module.php'
            || basename(dirname($moduleFile->getPath(), 2)) !== 'GXModules') {
            return;
        }
        
        $this->components[] = GX4Module::create($this->determineFqn($moduleFile->getPathname()));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return GX4Module::type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function createRegistry(): ComponentsRegistry
    {
        return GX4ModuleRegistry::create(...$this->components);
    }
}