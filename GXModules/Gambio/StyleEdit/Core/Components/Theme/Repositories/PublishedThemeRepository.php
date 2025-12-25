<?php
/*--------------------------------------------------------------------------------------------------
    PublishedThemeRepository.php 2020-02-13
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Repositories;

use Exception;
use ExistingDirectory;
use Gambio\StyleEdit\StyleEditConfiguration;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class PublishedThemeRepository
 * @package Gambio\StyleEdit\Core\Components\Theme\Repositories
 */
class PublishedThemeRepository
{
    /**
     * @var StyleEditConfiguration
     */
    protected $configuration;
    
    
    /**
     * PublishedThemeRepository constructor.
     *
     * @param StyleEditConfiguration $configuration
     */
    public function __construct(StyleEditConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }
    
    
    /**
     * @return string
     * @throws Exception
     */
    public function createPreviewFolder(): string
    {
        $dirName = 'theme' . date('ymdHis');
        $dirPath = $this->configuration->publicFolderPath() . 'tmp/' . $dirName;
        
        if (file_exists($dirPath)) {
            $this->removeDir($dirPath);
        }
        $this->recursiveMakeDir($dirPath);
        return 'public/tmp/' . $dirName;
    }
    
    
    /**
     * @param $dir
     * @param $max_age
     */
    public function removeOldPreviewThemes($dir, $max_age): void
    {
        $limit = time() - $max_age;
        
        if (file_exists($dir)) {
            $it    = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    $filename = $file->getRealPath() . DIRECTORY_SEPARATOR . 'theme.json';
                    if (file_exists($filename)
                        && filemtime($filename) < $limit) {
                        $this->removeDir($file->getRealPath());
                    }
                }
            }
        }
    }
    
    
    /**
     * @param $dirPath
     */
    protected function removeChildren($dirPath): void
    {
        $it       = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $contents = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        $files    = [];
    
        /** @var \SplFileInfo $file */
        foreach ($contents as $file) {
            $files[] = $file;
        }
    
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }
    
    
    /**
     * @param $dirPath
     */
    protected function removeDir($dirPath): void
    {
        if (file_exists($dirPath)) {
            $this->removeChildren($dirPath);
            rmdir($dirPath);
        }
    }
    
    
    /**
     * @param ExistingDirectory $existingDirectory
     */
    public function clearPublishedTheme(ExistingDirectory $existingDirectory): void
    {
        $this->removeChildren($existingDirectory->getDirPath());
    }

    /**
     * @param ExistingDirectory $existingDirectory
     */
    public function clearTemplatesC(ExistingDirectory $existingDirectory): void
    {
        $this->removeChildren($existingDirectory->getDirPath());
    }

    
    /**
     * @param $path
     *
     * @throws \Exception
     */
    protected function recursiveMakeDir(
        $path
    ): void {
        $path    = str_replace("\\", DIRECTORY_SEPARATOR, $path);
        $path    = explode(DIRECTORY_SEPARATOR, $path);
        $rebuild = '';
        
        $shopStartDirectory = dirname(__DIR__, 7);
        
        foreach ($path AS $p) {
            if (false !== strpos($p, ':') || empty($p)) {
                continue;
            }
            
            $rebuild .= "/$p";
    
            if (strpos($rebuild, $shopStartDirectory) === false) {
                
                continue;
            }
            
            if (!is_dir($rebuild) && !mkdir($rebuild) && !is_dir($rebuild)) {
                throw new Exception('Impossible to create directory ' . $rebuild);
            }
        }
    }
    
    
}