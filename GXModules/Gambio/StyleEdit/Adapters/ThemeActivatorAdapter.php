<?php
/*--------------------------------------------------------------------------------------------------
    ThemeActivatorAdapter.php 2019-10-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Adapters;

use Exception;
use Gambio\StyleEdit\Adapters\Interfaces\ThemeActivatorAdapterInterface;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use StaticGXCoreLoader;
use ThemeService;

/**
 * Class ThemeActivatorAdapter
 * @package Gambio\StyleEdit\Adapters
 */
class ThemeActivatorAdapter implements ThemeActivatorAdapterInterface, SingletonStrategyInterface
{
    
    /**
     * @var ThemeService
     */
    protected $themeService;
    
    
    /**
     * ThemeActivatorAdapter constructor.
     *
     * @param ThemeService $themeService
     */
    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }
    
    
    /**
     * @return static
     * @codeCoverageIgnore
     */
    public static function create(): self
    {
        /** @var ThemeService $themeService */
        $themeService = StaticGXCoreLoader::getService('Theme');
        return new static($themeService);
    }
    
    
    /**
     * @param string $themeId
     *
     * @return mixed|void
     * @throws Exception
     */
    public function activateTheme(string $themeId)
    {
        $this->themeService->activateTheme($themeId);
    }
}