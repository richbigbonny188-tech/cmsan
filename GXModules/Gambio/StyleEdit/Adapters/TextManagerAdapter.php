<?php
/*--------------------------------------------------------------------------------------------------
    TextManagerAdapter.php 2021-10-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace GXModules\Gambio\StyleEdit\Adapters;

use Gambio\Core\TextManager\Services\TextManager;
use GXModules\Gambio\StyleEdit\Adapters\Interfaces\TextManagerAdapterInterface;
use LegacyDependencyContainer;

/**
 *
 */
class TextManagerAdapter implements TextManagerAdapterInterface
{
    /**
     * @var TextManager
     */
    protected $textManager;
    
    
    /**
     * @param TextManager $textManager
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * @return TextManagerAdapter
     */
    public static function create()
    {
        $textManager = LegacyDependencyContainer::getInstance()->get(TextManager::class);
    
        return new self($textManager);
    }
    
    
    /**
     * @inheritcDoc
     */
    public function getPhraseText(string $phrase, string $section, int $languageId = null): string
    {
        return $this->textManager->getPhraseText($phrase, $section, $languageId);
    }
}
