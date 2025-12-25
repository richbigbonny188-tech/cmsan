<?php
/*--------------------------------------------------------------------------------------------------
    ThemeExtenderRepository.php 2019-12-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Repositories;

use Exception;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\StyleEditConfiguration;
use stdClass;

/**
 * Class ThemeExtenderRepository
 * @package Gambio\StyleEdit\Core\Components\Theme\Repositories
 */
class ThemeExtenderRepository
{
    /**
     * @var StyleEditConfiguration
     */
    private $configuration;
    /**
     * @var FileIO
     */
    private $fileIO;
    
    
    /**
     * ThemeExtenderRepository constructor.
     *
     * @param FileIO                 $fileIO
     * @param StyleEditConfiguration $configuration
     */
    public function __construct(FileIO $fileIO, StyleEditConfiguration $configuration)
    {
        
        $this->fileIO        = $fileIO;
        $this->configuration = $configuration;
    }
    
    
    /**
     * @param stdClass $data
     */
    public function createThemeForData(stdClass $data)
    {
        if (!isset($data->id)) {
            throw  new \InvalidArgumentException('Theme ID is required for creating a new theme!');
        }
        if ($this->fileIO->exists($this->configuration->themesFolderPath() . $data->id)) {
            throw  new \InvalidArgumentException('Theme ' . $data->id . ' already exists!');
        }
    
        $destinationPath = $this->configuration->themesFolderPath() . $data->id;
        $this->fileIO->createDirectory($destinationPath);
        $this->fileIO->createDirectory($destinationPath.'/config');
        $this->fileIO->createDirectory($destinationPath.'/styles/styleedit');
        $this->fileIO->createDirectory($destinationPath.'/variants');
        
        
        $filename        = $destinationPath . DIRECTORY_SEPARATOR . 'theme.json';
        
        $this->fileIO->write($data, $filename);
    }
    
    
    /**
     * @param $sourceThemeId
     * @param $destinationThemeId
     *
     * @throws Exception
     */
    public function copySettings($sourceThemeId, $destinationThemeId): void
    {
        $sourcePath = $this->configuration->themesFolderPath() . $sourceThemeId . DIRECTORY_SEPARATOR;
        $destinationPath = $this->configuration->themesFolderPath() . $destinationThemeId . DIRECTORY_SEPARATOR;
        
        $occurrences = $this->fileIO->recursiveRegexSearch($sourcePath, '/settings.json/m');
        foreach ($occurrences as $sourceFile){
            $destinationFile = str_replace($sourcePath, $destinationPath, $sourceFile);
            if ($this->fileIO->exists($destinationFile)) {
                $this->fileIO->delete($destinationFile);
            }
            $this->fileIO->createDirectory(dirname($destinationFile));
            $this->fileIO->recursive_copy($sourceFile, $destinationFile);
            
        }
    }
    
}