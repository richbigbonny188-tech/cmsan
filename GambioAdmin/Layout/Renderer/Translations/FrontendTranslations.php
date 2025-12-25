<?php
/* --------------------------------------------------------------
 FrontendTranslations.php 2020-08-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Translations;

/**
 * Class FrontendTranslations
 * @package Gambio\Admin\Layout\Renderer\Translations
 */
class FrontendTranslations
{
    /**
     * @var Translations
     */
    private $js;
    
    /**
     * @var Translations
     */
    private $vue;
    
    /**
     * @var Translations
     */
    private $jsEngine;
    
    
    /**
     * FrontendTranslations constructor.
     *
     * @param Translations $js
     * @param Translations $vue
     * @param Translations $jsEngine
     */
    public function __construct(Translations $jsEngine, Translations $js, Translations $vue)
    {
        $this->js       = $js;
        $this->vue      = $vue;
        $this->jsEngine = $jsEngine;
    }
    
    
    /**
     * Checks if at least one js translation is available.
     *
     * @return bool
     */
    public function hasJsTranslations(): bool
    {
        return !$this->js->isEmpty();
    }
    
    
    /**
     * Adds the translation data of a whole section to the js translations.
     *
     * @param string $section
     */
    public function addJsSection(string $section): void
    {
        $this->js->addSection($section);
    }
    
    
    /**
     * Adds a new translation to the js translations.
     *
     * @param string      $key
     * @param string      $phraseOrSection
     * @param string|null $section
     */
    public function addJsTranslation(string $key, string $phraseOrSection, string $section = null): void
    {
        $this->js->add($key, $phraseOrSection, $section);
    }
    
    
    /**
     * Serializes the js translations into a json string.
     *
     * @return string
     */
    public function serializeJsTranslations(): string
    {
        return json_encode($this->js);
    }
    
    
    /**
     * Checks if at least one vue translation is available.
     *
     * @return bool
     */
    public function hasVueTranslations(): bool
    {
        return !$this->vue->isEmpty();
    }
    
    
    /**
     * Adds the translation data of a whole section to the vue translations.
     *
     * @param string $section
     */
    public function addVueSection(string $section): void
    {
        $this->vue->addSection($section);
    }
    
    
    /**
     * Adds a new translation to the vue translations.
     *
     * @param string      $key
     * @param string      $phraseOrSection
     * @param string|null $section
     */
    public function addVueTranslation(string $key, string $phraseOrSection, string $section = null): void
    {
        $this->vue->add($key, $phraseOrSection, $section);
    }
    
    
    /**
     * Serializes the vue translations into a json string.
     *
     * @return string
     */
    public function serializeVueTranslations(): string
    {
        return json_encode($this->vue);
    }
    
    
    /**
     * Checks if at least one jsEngine translation is available.
     *
     * @return bool
     */
    public function hasJsEngineTranslations(): bool
    {
        return !$this->jsEngine->isEmpty();
    }
    
    
    /**
     * Adds the translation data of a whole section to the jsEngine translations.
     *
     * @param string $section
     */
    public function addJsEngineSection(string $section): void
    {
        $this->jsEngine->addSection($section);
    }
    
    
    /**
     * Adds a new translation to the jsEngine translations.
     *
     * @param string      $key
     * @param string      $phraseOrSection
     * @param string|null $section
     */
    public function addJsEngineTranslation(string $key, string $phraseOrSection, string $section = null): void
    {
        $this->jsEngine->add($key, $phraseOrSection, $section);
    }
    
    
    /**
     * Serializes the jsEngine translations into a json string.
     *
     * @return string
     */
    public function serializeJsEngineTranslations(): string
    {
        return json_encode($this->jsEngine);
    }
}