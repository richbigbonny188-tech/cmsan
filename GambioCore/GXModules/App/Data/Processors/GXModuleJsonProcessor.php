<?php
/* --------------------------------------------------------------
   GXModuleJsonProcessor.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\GXModules\Model\Collections\GXModuleJsonRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\GXModuleJson;
use SplFileInfo;

/**
 * Class GXModuleJsonProcessor
 *
 * @package Gambio\Core\GXModules\App\Data\Processors
 */
class GXModuleJsonProcessor implements GXModuleComponentProcessor
{
    /**
     * @var GXModuleJson[]
     */
    private $components;
    
    
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
        if ($moduleFile->getFilename() !== 'GXModule.json'
            || basename(dirname($moduleFile->getPath(), 2)) !== 'GXModules') {
            return;
        }
        
        $this->components[] = GXModuleJson::create($moduleFile->getPathname());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return GXModuleJson::type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function createRegistry(): ComponentsRegistry
    {
        return GXModuleJsonRegistry::create(...$this->components);
    }
}