<?php
/* --------------------------------------------------------------
  StyleEdit3ThemeConfigurationReader.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories;

use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ThemeConfigurationReaderInterface;

/**
 * Class StyleEdit3ThemeConfigurationReader
 */
class StyleEdit3ThemeConfigurationReader extends StyleEdit3ConfigurationReader
    implements StyleEdit3ThemeConfigurationReaderInterface
{
    /**
     * @var string
     */
    protected const PATH_PREFIX = 'themes/';
    
    
    /**
     * @return string[] paths to a StyleEdit3 json file from the root of the shop
     * @throws FileNotFoundException
     */
    public function configurations(): array
    {
        $result = [];
        
        foreach ($this->filesystem->listContents() as $theme) {
            
            if ($theme['type'] === 'dir') {
                
                $styleEditDirectory = $theme['path'] . '/styles/styleedit';
                
                if ($this->filesystem->has($styleEditDirectory) && !$this->themeIsPreview($theme['path'])) {
                    
                    foreach ($this->filesystem->listContents($styleEditDirectory) as $styleEditFile) {
                        
                        if ($styleEditFile['type'] === 'file' && $styleEditFile['extension'] === 'json') {
                            
                            $result[] = self::PATH_PREFIX . $styleEditFile['path'];
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param string $themeDirectory
     *
     * @return bool
     * @throws FileNotFoundException
     */
    protected function themeIsPreview(string $themeDirectory): bool
    {
        $themeJsonPath = $themeDirectory . '/theme.json';
        
        if ($this->filesystem->has($themeJsonPath)) {
            
            $themeJson = json_decode($this->filesystem->read($themeJsonPath), false);
            
            return isset($themeJson->preview) && $themeJson->preview === true;
        }
        
        return false;
    }
}