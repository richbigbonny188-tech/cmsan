<?php
/* --------------------------------------------------------------
   ServiceProviderProcessor.php 2021-05-14
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
use Gambio\Core\GXModules\Model\Collections\ServiceProviderRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\ServiceProvider;
use SplFileInfo;

/**
 * Class ServiceProviderProcessor
 *
 * @package Gambio\Core\GXModules\App\Data\Processors
 */
class ServiceProviderProcessor implements GXModuleComponentProcessor
{
    use DeterminesFqn;
    
    /**
     * @var ServiceProvider[]
     */
    private $components;
    
    
    /**
     * ServiceProviderProcessor constructor.
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
        if (substr($moduleFile->getFilename(), -19) !== 'ServiceProvider.php') {
            return;
        }
        
        $this->components[] = ServiceProvider::create($this->determineFqn($moduleFile->getPathname()));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return ServiceProvider::type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function createRegistry(): ComponentsRegistry
    {
        return ServiceProviderRegistry::create(...$this->components);
    }
}