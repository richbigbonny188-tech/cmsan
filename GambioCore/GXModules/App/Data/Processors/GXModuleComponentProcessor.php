<?php
/* --------------------------------------------------------------
   GXModuleComponentProcessor.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\GXModules\Model\ComponentsRegistry;
use SplFileInfo;

/**
 * Interface GXModuleComponentProcessor
 *
 * @package Gambio\Core\GXModules\App\Data
 */
interface GXModuleComponentProcessor
{
    /**
     * Resets the internal state of the processor.
     */
    public function resetInternalState(): void;
    
    
    /**
     * @param SplFileInfo $moduleFile
     */
    public function processModuleFile(SplFileInfo $moduleFile): void;
    
    
    /**
     * @return string
     */
    public function getType(): string;
    
    
    /**
     * @return ComponentsRegistry
     */
    public function createRegistry(): ComponentsRegistry;
}