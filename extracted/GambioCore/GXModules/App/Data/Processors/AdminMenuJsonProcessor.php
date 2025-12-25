<?php
/* --------------------------------------------------------------
   AdminMenuJsonProcessor.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\GXModules\Model\Collections\AdminMenuJsonRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\AdminMenuJson;
use SplFileInfo;

/**
 * Class AdminMenuJsonProcessor
 *
 * @package Gambio\Core\GXModules\App\Data\Processors
 */
class AdminMenuJsonProcessor implements GXModuleComponentProcessor
{
    /**
     * @var AdminMenuJson[]
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
        if (substr($moduleFile->getFilename(), -10) !== '.menu.json' || basename($moduleFile->getPath()) !== 'Menu') {
            return;
        }
        
        $this->components[] = AdminMenuJson::create($moduleFile->getPathname());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return AdminMenuJson::type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function createRegistry(): ComponentsRegistry
    {
        return AdminMenuJsonRegistry::create(...$this->components);
    }
}