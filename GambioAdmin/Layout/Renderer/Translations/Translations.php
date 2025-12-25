<?php
/* --------------------------------------------------------------
 Translations.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Translations;

use Gambio\Core\TextManager\Services\TextManager;
use JsonSerializable;

class Translations implements JsonSerializable
{
    /**
     * @var array
     */
    private $translations = [];
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * Translator constructor.
     *
     * @param TextManager $textManager
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * Adds a new translation.
     *
     * ```php
     * // providing custom key
     * $instance->add('save', 'BUTTON_SAVE', 'admin_buttons');
     *
     * // use phrase as key
     * $instance->add('BUTTON_SAVE', 'admin_buttons');
     * ```
     *
     * @param string      $key
     * @param string      $phraseOrSection
     * @param string|null $section
     */
    public function add(string $key, string $phraseOrSection, string $section = null): void
    {
        $phrase  = $section ? $phraseOrSection : $key;
        $section = $section ? : $phraseOrSection;
        
        $this->translations[$key] = $this->textManager->getPhraseText($phrase, $section);
    }
    
    
    /**
     * Adds a whole translated section.
     *
     * @param string $section
     */
    public function addSection(string $section): void
    {
        $this->translations[$section] = $this->textManager->getSectionPhrases($section);
    }
    
    
    /**
     * Returns the translations that will be json serialized.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->translations;
    }
    
    
    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->translations);
    }
}