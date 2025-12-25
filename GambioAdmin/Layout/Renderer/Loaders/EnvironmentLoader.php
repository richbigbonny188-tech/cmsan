<?php
/* --------------------------------------------------------------
 EnvironmentLoader.php 2020-02-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\TemplateEngine\LayoutData;

/**
 * Class EnvironmentLoader
 * @package Gambio\Admin\Layout\Renderer\Loaders
 */
class EnvironmentLoader implements Loader
{
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $devEnvironment = file_exists(__DIR__ . '/../../../../.dev-environment');
        
        $data->assign('environment', $devEnvironment ? 'development' : 'production');
        $data->assign('suffix', $devEnvironment ? '.min' : '');
    }
}