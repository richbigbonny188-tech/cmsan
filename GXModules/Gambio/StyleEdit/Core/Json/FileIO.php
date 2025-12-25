<?php
/*--------------------------------------------------------------------------------------------------
    FileIO.php 2020-02-13
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Json;

use DirectoryIterator;
use Exception;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;
use stdClass;

/**
 * Class ConfigurationFileReader
 * @package Gambio\StyleEdit\Core\Components\Theme
 */
class FileIO implements SingletonStrategyInterface
{
    /**
     * @param $path
     *
     * @return array
  */
    public function listDirectory($path): array
    {
        
        $themeDirectoryPaths = [];
        if (is_dir($path)) {
            foreach (new DirectoryIterator($path) as $directory) {
                if (!$directory->isDot() && $directory->isDir() && $directory->isReadable()) {
                    $themeDirectoryPaths[] = $path . $directory->getBasename() . DIRECTORY_SEPARATOR;
                }
            }
        }
        
        return $themeDirectoryPaths;
    }
    
    
    /**
     * @param $path
     *
     * @return array
     */
    public function listDirectoryFiles($path): array
    {
        $themeDirectoryPaths = [];
        if (is_dir($path)) {
            foreach (new DirectoryIterator($path) as $directory) {
                if (!$directory->isDot() && !$directory->isDir() && $directory->isReadable()) {
                    $themeDirectoryPaths[] = $directory->getBasename();
                }
            }
        }
        
        return $themeDirectoryPaths;
    }
    
    
    /**
     * removes a directory and all it's contents
     *
     * @param  $src
     *
     * @codeCoverageIgnore
     */
    public function recursive_rmdir(string $src)
    {
        if (empty($src)) {
            return;
        }
    
        $contents = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
                                                  RecursiveIteratorIterator::CHILD_FIRST);
    
        $files = [];
    
        /** @var \SplFileInfo $file */
        foreach ($contents as $file) {
            $files[] = $file;
        }
    
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getType() === 'dir') {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    
        unset($contents, $files);
    
        if (is_dir($src)) {
            rmdir($src);
        }
    }
    
    
    /**
     * Copy the contents of a directory recursively into another
     *
     * @param string $source      path
     * @param string $destination path
     *
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function recursive_copy($source, $destination)
    {
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }
        
        if (is_file($source)) {
            return copy($source, $destination);
        }
        //check is_dir twice to avoid race problems
        if (is_dir($destination) || mkdir($destination) || is_dir($destination)) {
            
            $dir = dir($source);
            try {
                while (false !== $entry = $dir->read()) {
                    //  Skip pointers
                    if ($entry == '.' || $entry == '..') {
                        continue;
                    }
                    
                    $this->recursive_copy($source . DIRECTORY_SEPARATOR . $entry,
                                          $destination . DIRECTORY_SEPARATOR . $entry);
                }
            }
            finally {
                $dir->close();
            }
        }
        
        // Clean up
        
        return true;
    }
    
    
    /**
     * @param string   $filename
     * @param stdClass $data
     *
     * @throws Exception
     */
    public function patch(string $filename, stdClass $data): void
    {
        $configuration = $this->read($filename);
        
        foreach ($data as $key => $value) {
            $configuration->$key = $value;
            if ($configuration->$key === null) {
                unset($configuration->$key);
            }
        }
        $this->write($configuration, $filename);
    }
    
    
    /**
     * @param $filePath
     *
     * @return array|mixed
     * @throws Exception
     */
    public function read($filePath)
    {
        if ($this->exists($filePath)) {
            $result = json_decode(file_get_contents($filePath), false);
        } else {
            throw new RuntimeException("Invalid file path {$filePath}");
        }
        
        if (json_last_error()) {
            throw new RuntimeException("'$filePath' isn't a valid JSON file!");
        }
        
        return $result;
    }
    
    /**
     * @param $object
     * @param $filePath
     *
     * @return bool|false|int
     */
    public function write($object, $filePath)
    {
        return file_put_contents($filePath, json_encode($object, JSON_PRETTY_PRINT));
    }
    
    
    /**
     * @param $filePath
     *
     * @return bool
     */
    public function exists($filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * @param string $path
     * @param int $permission
     * @param bool $recursive
     *
     * @return bool
     */
    public function createDirectory(string $path, int $permission = 0755, bool $recursive = true): bool
    {
        if (is_dir($path)) {
            
            return true;
        }
        
        return mkdir($path, $permission, $recursive);
    }
    
    
    /**
     * @param $folder
     * @param $pattern
     *
     * @return array
     */
    public function recursiveRegexSearch($folder, $pattern) {
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $pattern, RegexIterator::MATCH);
        $fileList = array();
        foreach($files as $file) {
            $fileList[] = $file->getPathname();
        }
        return $fileList;
    }
    
    
    /**
     * @param string $destinationFile
     */
    public function delete(string $destinationFile)
    {
        unlink($destinationFile);
    }
    
    
    /**
     * @param string $path
     * @param int    $mode
     */
    public function recursiveChmod(string $path, int $mode = 0777): void
    {
        chmod($path, $mode);
        
        if (is_dir($path)){
            
            $files = new DirectoryIterator($path);
            
            foreach ($files as $file) {
                
                if ($file->isDot()) {
                    
                    continue;
                }
                
                $this->recursiveChmod($file->getPathname(), $mode);
            }
        }
    }
}