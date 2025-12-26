<?php
/* --------------------------------------------------------------
   TemplateDetailsReader.php 2021-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Reader;

use Gambio\AdminFeed\Adapters\GxAdapterTrait;
use Gambio\AdminFeed\Services\ShopInformation\Settings;

/**
 * Class TemplateDetailsReader
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Reader
 */
class TemplateDetailsReader
{
    use GxAdapterTrait;
    
    /**
     * @var Settings
     */
    private $settings;
    
    
    /**
     * TemplateDetailsReader constructor.
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }
    
    
    /**
     * Returns a list of available templates.
     *
     * @return array
     */
    public function getAvailableTemplates()
    {
        $return   = [];
        $themes   = (array)glob($this->settings->getBaseDirectory() . 'themes/*', GLOB_ONLYDIR);
        $previews = (array)glob($this->settings->getBaseDirectory() . 'themes/*_preview', GLOB_ONLYDIR);
        
        foreach (array_diff($themes, $previews) as $theme) {
            $version = null;
            if (file_exists($theme . '/theme.json')) {
                $json    = json_decode(file_get_contents($theme . '/theme.json'), true);
                $version = $json['version'] ?? null;
            }
            
            $return[basename($theme)] = $version;
        }
        
        return $return;
    }
    
    
    /**
     * Returns the name of the active template.
     *
     * @return string
     */
    public function getActiveTemplate()
    {
        return 'themes/' . $this->gxAdapter()->getThemeControl()->getCurrentTheme();
    }
    
    
    /**
     * Returns the version of the active template.
     *
     * @return string
     */
    public function getActiveTemplateVersion()
    {
        return $this->gxAdapter()->getThemeControl()->getThemeVersion();
    }
}