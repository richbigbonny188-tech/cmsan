<?php
/* --------------------------------------------------------------
 IncludeBootstrap.php 2022-03-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Bootstrap;

/**
 * Trait IncludeBootstrap
 * @package Gambio\Admin\Modules\Bootstrap
 * @codeCoverageIgnore
 */
trait IncludeBootstrap
{
    /**
     * Includes the bootstrap 4 frontend asset tags into the dataset.
     *
     * @param array $data
     */
    private function includeBootstrapAssets(array &$data): void
    {
        $scripts = 'dynamic_script_assets';
        $styles  = 'dynamic_style_assets';
        
        $styleTag        = <<<STYLE
<link rel="stylesheet" href="{$this->url->base()}/GambioAdmin/build/styles/bootstrap.css">
STYLE;
        $scriptTag       = <<<SCRIPT
<script type="text/javascript" src="{$this->url->base()}/GambioAdmin/build/js/bootstrap.js"></script>
SCRIPT;
        $hasScriptAssets = array_key_exists($scripts, $data);
        $hasStyleAssets  = array_key_exists($styles, $data);
        
        $data[$scripts] = $hasScriptAssets ? $data[$scripts] . PHP_EOL . $scriptTag : $scriptTag;
        $data[$styles]  = $hasStyleAssets ? $data[$styles] . PHP_EOL . $styleTag : $styleTag;
    }
}