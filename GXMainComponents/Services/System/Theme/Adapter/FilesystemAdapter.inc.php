<?php
/* --------------------------------------------------------------
   FilesystemAdapter.inc.php 2022-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;

/**
 * Class FilesystemAdapter
 */
class FilesystemAdapter
{
    protected FilesystemOperator $driver;
    
    
    /**
     * FilesystemAdapter constructor.
     *
     * @param FilesystemOperator $driver
     */
    public function __construct(FilesystemOperator $driver)
    {
        $this->driver = $driver;
    }
    
    
    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     * @throws FilesystemException
     * @throws UnableToCheckFileExistence
     */
    public function has(string $path): bool
    {
        return $this->driver->fileExists($path);
    }
    
    
    /**
     * Read a file.
     *
     * @param string $path The path to the file.
     *
     * @return string|false The file contents or false on failure.
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function read(string $path)
    {
        if (!$this->driver->fileExists($path)) {
            return false;
        }
        return $this->driver->read($path);
    }
    
    
    /**
     * Retrieves a read-stream for a path.
     *
     * @param string $path The path to the file.
     *
     * @return resource|false The path resource or false on failure.
     * @throws FileNotFoundException
     *
     */
    public function readStream($path)
    {
        return $this->driver->readStream($path);
    }
    
    
    /**
     * List contents of a directory.
     *
     * @param string $directory The directory to list.
     * @param bool   $recursive Whether to list recursively.
     *
     * @return array A list of file metadata.
     */
    public function listContents($directory = '', $recursive = false)
    {
        $result = [];
        
        foreach ($this->driver->listContents($directory, $recursive) as $file) {
            
            if ($file instanceof \League\Flysystem\FileAttributes) {
                $pathParts = pathinfo($file->path());
                
                $result[] = [
                    'path'      => $file->path(),
                    'basename'  => $pathParts['basename'],
                    'extension' => $pathParts['extension'],
                    'type'      => 'file'
                ];
            }
            
            if ($file instanceof \League\Flysystem\DirectoryAttributes) {
    
                $result[] = [
                    'path'      => $file->path(),
                    'basename'  => basename($file->path()),
                    'extension' => null,
                    'type'      => 'dir'
                ];
                
                if ($recursive) {
    
                    foreach ($this->listContents($file->path(), true) as $content) {
        
                        $result[] = $content;
                    }
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * Get a file's size.
     *
     * @param string $path The path to the file.
     *
     * @return int|false The file size or false on failure.
     * @throws FileNotFoundException
     *
     */
    public function getSize($path)
    {
        return $this->driver->fileSize($path);
    }
    
    
    /**
     * Get a file's mime-type.
     *
     * @param string $path The path to the file.
     *
     * @return string|false The file mime-type or false on failure.
     * @throws FileNotFoundException
     *
     */
    public function getMimetype($path)
    {
        return $this->driver->mimeType($path);
    }
    
    
    /**
     * Get a file's visibility.
     *
     * @param string $path The path to the file.
     *
     * @return string The visibility (public|private) or false on failure.
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     */
    public function getVisibility(string $path): string
    {
        return $this->driver->visibility($path);
    }
    
    
    /**
     * Write a new file.
     *
     * @param string $path     The path of the new file.
     * @param string $contents The file contents.
     * @param array  $config   An optional configuration array.
     *
     * @return bool True on success, false on failure.
     * @throws UnableToWriteFile
     * @throws FilesystemException
     *
     */
    public function write($path, $contents, array $config = [])
    {
        return $this->driver->write($path, $contents, $config);
    }
    
    
    /**
     * Write a new file using a stream.
     *
     * @param string   $path     The path of the new file.
     * @param resource $resource The file handle.
     * @param array    $config   An optional configuration array.
     *
     * @return bool True on success, false on failure.
     * @throws FileExistsException
     *
     * @throws InvalidArgumentException If $resource is not a file handle.
     */
    public function writeStream($path, $resource, array $config = [])
    {
        return $this->driver->writeStream($path, $resource, $config);
    }
    
    
    /**
     * Update an existing file.
     *
     * @param string $path     The path of the existing file.
     * @param string $contents The file contents.
     * @param array  $config   An optional configuration array.
     *
     * @return bool True on success, false on failure.
     * @throws FileNotFoundException
     *
     */
    public function update(string $path, string $contents, array $config = []): bool
    {
        try {
            if (!$this->driver->fileExists($path)) {
                throw new FileNotFoundException("File ($path) was not found and therefore can not be updated.");
            }
            $this->driver->write($path, $contents, $config);
            
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
    
    
    /**
     * Rename a file.
     *
     * @param string $path    Path to the existing file.
     * @param string $newpath The new path of the file.
     *
     * @return bool True on success, false on failure.
     * @throws FileNotFoundException Thrown if $path does not exist.
     *
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function rename(string $path, string $newpath): bool
    {
        $this->driver->move($path, $newpath);
        
        return true;
    }
    
    
    /**
     * Copy a file.
     *
     * @param string $path    Path to the existing file.
     * @param string $newpath The new path of the file.
     *
     * @return bool True on success, false on failure.
     * @throws FileNotFoundException Thrown if $path does not exist.
     *
     * @throws FileExistsException   Thrown if $newpath exists.
     */
    public function copy($path, $newpath)
    {
        return $this->driver->copy($path, $newpath)
               && $this->driver->setVisibility($newpath,
                                               AdapterInterface::VISIBILITY_PUBLIC);
    }
    
    
    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool True on success, false on failure.
     * @throws FileNotFoundException
     *
     */
    public function delete($path)
    {
        return $this->driver->delete($path);
    }
    
    
    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool True on success, false on failure.
     */
    public function deleteDir(string $dirname): bool
    {
        try {
            $this->driver->deleteDirectory($dirname);
            
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
    
    
    /**
     * Create a directory.
     *
     * @param string $dirname The name of the new directory.
     * @param array  $config  An optional configuration array.
     *
     * @return bool True on success, false on failure.
     */
    public function createDir(string $dirname, array $config = []): bool
    {
        try {
            $this->driver->createDirectory($dirname, $config);
            $this->driver->setVisibility($dirname, Visibility::PUBLIC);
            
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
    
    
    /**
     * Set the visibility for a file.
     *
     * @param string $path       The path to the file.
     * @param string $visibility One of 'public' or 'private'.
     *
     * @return bool True on success, false on failure.
     * @throws FileNotFoundException
     */
    public function setVisibility($path, $visibility)
    {
        try {
            if (!$this->driver->fileExists($path)) {
                throw new FileNotFoundException("File with path ($path) was not found and therefore the visibility can not be modified");
            }
            $this->driver->setVisibility($path, $visibility);
            
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
    
    
    /**
     * Create a file or update if exists.
     *
     * @param string $path     The path to the file.
     * @param string $contents The file contents.
     * @param array  $config   An optional configuration array.
     *
     * @return bool True on success, false on failure.
     */
    public function put(string $path, string $contents, array $config = []): bool
    {
        try {
            $this->driver->write($path, $contents, $config);
            
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
    
    
    /**
     * Create a file or update if exists.
     *
     * @param string   $path     The path to the file.
     * @param resource $resource The file handle.
     * @param array    $config   An optional configuration array.
     *
     * @return bool True on success, false on failure.
     * @throws InvalidArgumentException Thrown if $resource is not a resource.
     */
    public function putStream(string $path, $resource, array $config = []): bool
    {
        try {
            $this->driver->writeStream($path, $resource, $config);
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
    
    
    public function getAdapter()
    {
        //return $this->driver->
    }
}