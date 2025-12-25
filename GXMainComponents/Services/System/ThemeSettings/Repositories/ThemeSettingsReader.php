<?php
/* --------------------------------------------------------------
  ThemeSettingsReader.php 2019-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Repositories;

use FileNotFoundException;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsReaderInterface;
use stdClass;
use FilesystemAdapter;

/**
 * Class ThemeSettingsReader
 */
class ThemeSettingsReader implements ThemeSettingsReaderInterface
{
    /**
     * @var FilesystemAdapter
     */
    protected $themeDirectory;
    
    /**
     * @var stdClass[]
     */
    protected $themeJsons;
    
    
    /**
     * ThemeSettingsReader constructor.
     *
     * @param FilesystemAdapter $themeDirectory
     */
    public function __construct(FilesystemAdapter $themeDirectory)
    {
        $this->themeDirectory = $themeDirectory;
    }
    
    
    /**
     * @param string $id
     *
     * @return array
     * @throws FileNotFoundException
     */
    public function getById(string $id): array
    {
        foreach ($this->getAllThemeJsons() as $json) {
            
            if (isset($json[0]->id) && (string)$json[0]->id === $id) {
                
                return $json;
            }
        }
        
        throw new FileNotFoundException("No theme was found with the id ($id)");
    }
    
    
    /**
     * @return array
     * @throws FileNotFoundException
     */
    public function getAll(): array
    {
        return $this->getAllThemeJsons();
    }
    
    
    /**
     * @return stdClass[]
     * @throws FileNotFoundException
     */
    protected function getAllThemeJsons(): array
    {
        if ($this->themeJsons === null) {
            
            $this->readThemeJsons();
        }
        
        return $this->themeJsons;
    }
    
    
    /**
     * @throws FileNotFoundException
     */
    protected function readThemeJsons(): void
    {
        $jsonObjects = [];
        
        foreach ($this->themeDirectory->listContents() as $content) {
            
            if ($content['type'] === 'dir') {
                
                $themeJsonPath = $content['path'] . DIRECTORY_SEPARATOR . 'theme.json';
                
                if ($this->themeDirectory->has($themeJsonPath)) {
                    
                    $file = $this->themeDirectory->read($themeJsonPath);
                    $json = json_decode($file, false);
                    
                    if ($json instanceof stdClass && (!isset($json->preview) || $json->preview === false)) {
                        
                        $jsonObjects[] = [$json, $themeJsonPath];
                    }
                }
            }
        }
        
        $this->themeJsons = $jsonObjects;
    }
}