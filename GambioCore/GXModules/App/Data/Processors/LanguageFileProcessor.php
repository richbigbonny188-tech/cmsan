<?php
/* --------------------------------------------------------------
   LanguageFileProcessor.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\GXModules\Model\Collections\LanguageFileRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\LanguageFile;
use SplFileInfo;

/**
 * Class LanguageFileProcessor
 *
 * @package Gambio\Core\GXModules\App\Data\Processors
 */
class LanguageFileProcessor implements GXModuleComponentProcessor
{
    /**
     * @var LanguageFile[]
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
        if (substr($moduleFile->getFilename(), -13) !== '.lang.inc.php'
            || basename(dirname($moduleFile->getPath(), 1)) !== 'TextPhrases') {
            return;
        }
        
        $language = basename($moduleFile->getPath());
        
        $this->components[] = LanguageFile::create($moduleFile->getPathname(), $language);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return LanguageFile::type();
    }
    
    
    /**
     * @inheritDoc
     */
    public function createRegistry(): ComponentsRegistry
    {
        return LanguageFileRegistry::create(...$this->components);
    }
}