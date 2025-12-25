<?php
/* --------------------------------------------------------------
 Utility.php 2020-10-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Factories\Helper;

use Gambio\Admin\Layout\Menu\Models\Cached\MenuSettings;
use function str_replace;

/**
 * Class Utility
 * @package Gambio\Admin\Layout\Menu\Permissions
 */
class Utility
{
    public const TYPE_CONTROLLER = 'CONTROLLER';
    public const TYPE_PAGE       = 'PAGE';
    public const TYPE_ROUTE      = 'ROUTE';
    
    /**
     * @var MenuSettings
     */
    private $settings;
    
    
    /**
     * Utility constructor.
     *
     * @param MenuSettings $settings
     */
    public function __construct(MenuSettings $settings)
    {
        $this->settings = $settings;
    }
    
    
    /**
     * Determines the menu item type.
     * Currently, is is either "CONTROLLER" or "PAGE".
     *
     * @param string $url
     *
     * @return string
     */
    public function determineType(string $url): string
    {
        if (strpos($url, '.php') === false || strpos($url, '/api.php/v3/') === 0) {
            return self::TYPE_ROUTE;
        }
        
        return strpos($url, 'admin.php?do=') !== false ? self::TYPE_CONTROLLER : self::TYPE_PAGE;
    }
    
    
    /**
     * Determines the identifier values.
     *
     * The returned value depends on the menu item type and usually
     * equals either the controller- or legacy filename.
     *
     * @param string $url
     *
     * @return string
     */
    public function determineIdentifier(string $url): string
    {
        switch ($this->determineType($url)) {
            case self::TYPE_CONTROLLER:
                return str_replace("{$this->settings->adminUrl()}/admin.php?do=", '', $url);
            case self::TYPE_ROUTE:
                return str_replace($this->settings->baseUrl(), '', $url);
            case self::TYPE_PAGE:
            default:
                if (strpos($url, '.php?') !== false) {
                    $url = substr($url, 0, strpos($url, '.php?') + 4);
                }
                
                return str_replace("{$this->settings->adminUrl()}/", '', $url);
        }
    }
}