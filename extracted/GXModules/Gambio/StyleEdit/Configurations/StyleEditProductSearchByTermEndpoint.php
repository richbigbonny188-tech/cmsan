<?php
/*--------------------------------------------------------------------------------------------------
    StyleEditProductSearchByTermEndpoint.php 2020-05-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Configurations;

use Gambio\StyleEdit\ConfigurationValueInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\CurrentTheme;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Language\Entities\Language;

/**
 * Class StyleEditProductSearchByTrmEndpoint
 * @package Gambio\StyleEdit\Shop\Classes\Controllers
 */
class StyleEditProductSearchByTermEndpoint implements ConfigurationValueInterface
{
    /**
     * @return string
     */
    protected $baseUrl = '';

    /**
     * @var Language
     */
    protected $language;

    /**
     * @var CurrentTheme
     */
    protected $theme;


    /**
     * @return string
     */
    public function value(): string
    {
        return $this->baseUrl. "GXModules/Gambio/StyleEdit/Api/api.php/styleedit/".$this->language->code()."/productsearch/".$this->theme->id()."/";
    }

    /**
     * StyleEditProductSearchByTermEndpoint constructor.
     *
     * @param ShopBaseUrl $url
     * @param Language $language
     * @param CurrentTheme $theme
     */
    public function __construct(ShopBaseUrl $url, Language $language, CurrentThemeInterface $theme)
    {
        $this->language = $language;
        $this->theme = $theme;
        $this->baseUrl = $url->value();
    }
    
}
