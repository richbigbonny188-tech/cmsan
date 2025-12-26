<?php
/* --------------------------------------------------------------
 SmartyConfiguration.php 2023-03-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\TemplateEngine\Engines\Smarty;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\TemplateEngine\Engines\Smarty\Plugins\GetUserMod;
use Gambio\Core\TemplateEngine\Engines\Smarty\Plugins\LoadLanguageText;
use Gambio\Core\TemplateEngine\Engines\Smarty\Plugins\LoadTemplateData;
use Smarty;
use SmartyException;
use function Gambio\Core\Application\env;

/**
 * Class SmartyConfiguration
 * @package Gambio\Core\TemplateEngine\Engines\Smarty
 */
class SmartyConfiguration
{
    /**
     * @var GetUserMod
     */
    private $getUserMod;
    
    /**
     * @var LoadLanguageText
     */
    private $loadLanguageText;
    
    /**
     * @var LoadTemplateData
     */
    private $loadTemplateData;
    
    /**
     * @var Path
     */
    private $path;
    
    
    /**
     * SmartyConfiguration constructor.
     *
     * @param GetUserMod       $getUserMod
     * @param LoadLanguageText $loadLanguageText
     * @param LoadTemplateData $loadTemplateData
     * @param Path             $path
     */
    public function __construct(
        GetUserMod $getUserMod,
        LoadLanguageText $loadLanguageText,
        LoadTemplateData $loadTemplateData,
        Path $path
    ) {
        $this->getUserMod       = $getUserMod;
        $this->loadLanguageText = $loadLanguageText;
        $this->loadTemplateData = $loadTemplateData;
        $this->path             = $path;
    }
    
    
    public function load(Smarty $smarty): void
    {
        try {
            $templateDir = "{$this->path->base()}/GambioAdmin/Layout/ui/template";
            
            $smarty->setTemplateDir([$templateDir]);
            $smarty->setCompileDir("{$this->path->base()}/cache/smarty/");
            $smarty->registerResource('get_usermod', $this->getUserMod);
            $smarty->registerPlugin('function', 'load_language_text', $this->loadLanguageText->callback());
            $smarty->registerPlugin('function', 'load_template_data', $this->loadTemplateData->callback());
            
            if (env('SMARTY_MUTE_UNDEFINED_OR_NULL_WARNINGS', true)) {
                $smarty->muteUndefinedOrNullWarnings();
            }
        } catch (SmartyException $e) {
        }
    }
}
