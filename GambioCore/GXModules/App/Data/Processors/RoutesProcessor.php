<?php
/* --------------------------------------------------------------
   RoutesProcessor.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\GXModules\Model\Collections\RoutesRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\Routes;
use SplFileInfo;

/**
 * Class RoutesProcessor
 *
 * @package Gambio\Core\GXModules\App\Data\Processors
 */
class RoutesProcessor implements GXModuleComponentProcessor
{
    /**
     * @var Routes[]
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
        if ($moduleFile->getFilename() !== 'routes.php'
            || basename(dirname($moduleFile->getPath(), 2)) !== 'GXModules') {
            return;
        }
        
        $this->components[] = Routes::create($moduleFile->getPathname());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return Routes::type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function createRegistry(): ComponentsRegistry
    {
        return RoutesRegistry::create(...$this->components);
    }
}