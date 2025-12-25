<?php
/* --------------------------------------------------------------
 VuePageAction.php 2020-09-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Http;

use Gambio\Admin\Layout\Renderer\Translations\FrontendTranslations;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;

/**
 * Class VuePageAction
 * @package Gambio\Admin\Application\Http
 */
abstract class VuePageAction extends AdminAction
{
    private const JAVASCRIPT_ENGINE = 'GambioAdmin';
    
    /**
     * @var FrontendTranslations
     */
    private $translations;
    
    
    /**
     * Returns the name of the js entrypoint.
     *
     * @return string
     */
    abstract protected function jsEntrypoint(): string;
    
    
    /**
     * Renders the template.
     *
     * @param string $pageTitle
     * @param string $templatePath
     * @param array  $data
     *
     * @return string
     * @throws RenderingFailedException
     */
    public function render(string $pageTitle, string $templatePath, array $data = []): string
    {
        $data['isVuePage'] = true;
        $data['vuePage']   = $this->jsEntrypoint();
        
        $data['engine'] = self::JAVASCRIPT_ENGINE;
        
        return parent::render($pageTitle, $templatePath, $data);
    }
    
    
    /**
     * Adds a new vue page translation.
     *
     * @param string      $key
     * @param string      $phraseOrSection
     * @param string|null $section
     */
    protected function addVuePageTranslation(string $key, string $phraseOrSection, string $section = null): void
    {
        $this->translations->addVueTranslation($key, $phraseOrSection, $section);
    }
    
    
    /**
     * Uses the given frontend translations instance to add new translations.
     *
     * @param FrontendTranslations $translations
     */
    public function useFrontendTranslations(FrontendTranslations $translations): void
    {
        $this->translations = $translations;
    }
}