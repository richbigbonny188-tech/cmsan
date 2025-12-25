<?php
/* --------------------------------------------------------------
 AdminModuleAction.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Http;

use Gambio\Admin\Modules\Bootstrap\IncludeBootstrap;

/**
 * Class AdminModuleAction
 *
 * @package Gambio\Admin\Application\Http
 */
abstract class AdminModuleAction extends AdminAction
{
    use IncludeBootstrap;
    
    /**
     * @inheritDoc
     */
    public function render(string $pageTitle, string $templatePath, array $data = []): string
    {
        $this->includeBootstrapAssets($data);
        
        return parent::render($pageTitle, $templatePath, $data);
    }
}