<?php
/* --------------------------------------------------------------
 GambioAdminRenderer.php 2020-10-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer;

use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;
use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\TemplateEngine;

/**
 * Class GambioAdminRenderer
 * @package Gambio\Admin\Layout\Renderer
 */
class GambioAdminRenderer
{
    /**
     * @var TemplateEngine
     */
    private $engine;
    
    /**
     * @var GambioAdminLoader
     */
    private $loader;
    
    /**
     * @var LayoutData
     */
    private $data;
    
    
    /**
     * GambioAdminRenderer constructor.
     *
     * @param TemplateEngine    $engine
     * @param GambioAdminLoader $loader
     * @param LayoutData        $data
     */
    public function __construct(TemplateEngine $engine, GambioAdminLoader $loader, LayoutData $data)
    {
        $this->engine = $engine;
        $this->loader = $loader;
        $this->data   = $data;
    }
    
    
    /**
     * Renders the template.
     *
     * @param string $templatePath
     * @param array  $data
     *
     * @return string
     * @throws RenderingFailedException
     */
    public function render(string $templatePath, array $data): string
    {
        $this->loader->loadLayoutData($this->data);
        $templateData = array_merge($this->data->toArray(), $data);
        
        return $this->engine->render($templatePath, $templateData);
    }
}