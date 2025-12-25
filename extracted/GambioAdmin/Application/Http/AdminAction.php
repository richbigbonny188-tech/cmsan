<?php
/* --------------------------------------------------------------
 AdminAction.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Http;

use Gambio\Admin\Layout\Renderer\GambioAdminRenderer;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;

/**
 * Class AdminAction
 *
 * @package Gambio\Admin\Application\Http
 * @codeCoverageIgnore
 */
abstract class AdminAction extends AbstractAction
{
    /**
     * @var GambioAdminRenderer
     */
    private $renderer;
    
    /**
     * @var bool
     */
    private $isLargeLayout = false;
    
    
    /**
     * Admin action initialization.
     *
     * @param GambioAdminRenderer $renderer
     */
    public function initAdminAction(GambioAdminRenderer $renderer): void
    {
        $this->renderer = $renderer;
    }
    
    
    /**
     * Renders the template.
     *
     * @param string $pageTitle
     * @param string $templatePath
     * @param array  $data
     *
     * @return string
     *
     * @throws RenderingFailedException
     */
    public function render(string $pageTitle, string $templatePath, array $data = []): string
    {
        if ($this->isLargeLayout) {
            $data['isLargeLayout'] = true;
        }
        $data['pageTitle'] = $pageTitle;
        
        return $this->renderer->render($templatePath, $data);
    }
    
    
    /**
     * Replaces the renderer.
     *
     * This method can be inflected on child classes to provide a differently function renderer.
     *
     * @param GambioAdminRenderer $renderer
     */
    public function replaceRenderer(GambioAdminRenderer $renderer): void
    {
        $this->renderer = $renderer;
    }
    
    
    /**
     * Enables the large page layout.
     *
     * Technically, this method sets an internal flag, so the layout hides
     * the message stack and content navigation.
     */
    protected function enableLargePageLayout(): void
    {
        $this->isLargeLayout = true;
    }
}