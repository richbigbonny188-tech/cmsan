<?php
/*--------------------------------------------------------------------------------------------------
    ActiveThemeConfiguration.php 2021-05-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Adapters;

use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\ActiveThemeInterface;
use StaticGXCoreLoader;

/**
 * Class ActiveThemeConfigurationAdapter
 * @package Gambio\StyleEdit\Adapters
 */
class ActiveThemeConfigurationAdapter implements ActiveThemeInterface
{
    
    /**
     * @var string
     */
    protected $activeTheme;
    
    
    /**
     *  constructor.
     *
     * @param $activeTheme
     */
    public function __construct($activeTheme)
    {
        $this->activeTheme = $activeTheme;
    }
    
    
    /**
     * @return ActiveThemeConfigurationAdapter
     */
    public static function create(): ActiveThemeConfigurationAdapter
    {
        return new self(StaticGXCoreLoader::getThemeControl()->getCurrentTheme());
    }
    
    
    /**
     * @return mixed
     */
    public function value()
    {
        return $this->activeTheme;
    }
}