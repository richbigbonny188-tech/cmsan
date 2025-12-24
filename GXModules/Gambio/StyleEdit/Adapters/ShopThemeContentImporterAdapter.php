<?php
/*--------------------------------------------------------------------------------------------------
    ThemeContentImporter.php 2019-10-25
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Adapters;

use Gambio\StyleEdit\Adapters\Interfaces\ThemeContentImporterAdapterInterface;
use StaticGXCoreLoader;
use ThemeContentsParser;
use ThemeService;

/**
 * Class ThemeContentImporter
 * @package Gambio\StyleEdit\Adapters
 * @codeCoverageIgnore
 */
class ShopThemeContentImporterAdapter implements ThemeContentImporterAdapterInterface
{
    /**
     * @var ThemeService
     */
    protected $themeService;
    
    
    /**
     * @return ThemeService
     * @throws \Exception
     */
    protected function themeService(): ThemeService
    {
        if ($this->themeService === null) {
            
            $this->themeService = StaticGXCoreLoader::getService('Theme');
        }
        
        return $this->themeService;
    }
    
    
    /**
     * @param string $themeId
     *
     * @return mixed
     * @throws \Exception
     */
    public function importContentFromTheme(string $themeId)
    {
        $themeJsonPath   = DIR_FS_CATALOG . str_replace('/',
                                                          DIRECTORY_SEPARATOR,
                                                          'themes/' . $themeId . '/theme.json');
    
        if (file_exists($themeJsonPath)) {
        
            $themeJsonContent = file_get_contents($themeJsonPath);
            $themeJson        = json_decode($themeJsonContent, false);
        
            if ($themeJson !== false && isset($themeJson->contents)) {
            
                $themeIdObject = \ThemeId::create($themeId);
                $themeContents = ThemeContentsParser::parse($themeJson->contents);
                $this->themeService()->storeThemeContent($themeIdObject, $themeContents);
            }
        }
        
    }
}
