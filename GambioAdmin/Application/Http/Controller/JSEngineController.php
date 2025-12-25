<?php
/* --------------------------------------------------------------
 JSEngineController.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Http\Controller;

use Gambio\Admin\Layout\Renderer\GambioAdminRenderer;
use Gambio\Admin\Layout\Renderer\Translations\FrontendTranslations;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;

/**
 * Class JSEngineController
 * @package Gambio\Admin\Application\Http\Controller
 */
abstract class JSEngineController
{
    private const JAVASCRIPT_ENGINE = 'JSE';
    
    /**
     * @var GambioAdminRenderer
     */
    private $renderer;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    /**
     * @var FrontendTranslations
     */
    private $translations;
    
    
    /**
     * Initialization method.
     *
     * @param GambioAdminRenderer  $renderer
     * @param TextManager          $textManager
     * @param FrontendTranslations $translations
     */
    public function initializeJSEngineController(
        GambioAdminRenderer $renderer,
        TextManager $textManager,
        FrontendTranslations $translations
    ): void {
        $this->renderer     = $renderer;
        $this->textManager  = $textManager;
        $this->translations = $translations;
    }
    
    
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
        $data['engine']    = self::JAVASCRIPT_ENGINE;
        $data['pageTitle'] = $pageTitle;
        $data['_SESSION']  = $_SESSION;
        
        return $this->renderer->render($templatePath, $data);
    }
    
    
    public function addJsSectionTranslation(string $section): void
    {
        $this->translations->addJsEngineSection($section);
    }
    
    
    public function translate(string $phrase, string $section, int $languageId = null): string
    {
        return $this->textManager->getPhraseText($phrase, $section, $languageId);
    }
}