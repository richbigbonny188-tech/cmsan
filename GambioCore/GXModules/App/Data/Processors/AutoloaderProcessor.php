<?php
/* --------------------------------------------------------------
   AutoloaderProcessor.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\GXModules\Model\Collections\AutoloaderRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\Autoloader;
use SplFileInfo;

/**
 * Class AutoloaderProcessor
 *
 * @package Gambio\Core\GXModules\App\Data\Processors
 */
class AutoloaderProcessor implements GXModuleComponentProcessor
{
    /**
     * @var Autoloader[]
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
        if ($moduleFile->getFilename() !== 'autoload.php') {
            return;
        }
        
        $this->components[] = Autoloader::create($moduleFile->getPathname());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return Autoloader::type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function createRegistry(): ComponentsRegistry
    {
        return AutoloaderRegistry::create(...$this->components);
    }
}