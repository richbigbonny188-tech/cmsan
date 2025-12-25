<?php
/* --------------------------------------------------------------
   FlysystemAdapter.php 2022-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filesystem;

use Gambio\Core\Filesystem\Exceptions\FilesystemException;
use Gambio\Core\Filesystem\Interfaces\Filesystem;
use League\Flysystem\FilesystemOperator;

/**
 * Class Filesystem
 *
 * @package Gambio\Core\Filesystem
 */
class FlysystemAdapter implements Filesystem
{
    protected FilesystemOperator $flysystem;
    protected string $baseDirectory;
    
    
    /**
     * FilesystemAdapter constructor.
     *
     * @param FilesystemOperator $flysystem
     * @param string    $baseDirectory
     */
    public function __construct(FilesystemOperator $flysystem, string $baseDirectory)
    {
        $this->flysystem     = $flysystem;
        $this->baseDirectory = $baseDirectory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function has(string $path): bool
    {
        try {
            return $this->flysystem->fileExists($path);
        } catch (\League\Flysystem\FilesystemException $e) {
            return false;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function read(string $path): ?string
    {
        try {
            return $this->flysystem->read($path);
        } catch (\League\Flysystem\FilesystemException $e) {
            return null;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function list(string $directory, bool $recursive = false): \Gambio\Core\Filesystem\Interfaces\DirectoryItems
    {
        $files = [];
        try {
            foreach ($this->flysystem->listContents($directory, $recursive) as $fileData) {
                $files[] = DirectoryItem::createFromFlysystem($fileData,
                                                              $this->baseDirectory);
            }
        } catch (\League\Flysystem\FilesystemException $e) {
            return DirectoryItems::create();
        }
    
        return DirectoryItems::create(...$files);
    }
    
    
    /**
     * @inheritDoc
     */
    public function create(string $path, string $content = ''): void
    {
        try {
            $this->flysystem->write($path, $content);
        } catch (\League\Flysystem\FilesystemException $e) {
            $message = "Can not create file in path $path: {$e->getMessage()}";
            
            throw new FilesystemException($message, $e->getCode(), $e);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function update(string $path, string $content): void
    {
        try {
            $this->flysystem->write($path, $content);
        } catch (\League\Flysystem\FilesystemException $e) {
            $message = "Can not update file in path $path: {$e->getMessage()}";
        
            throw new FilesystemException($message, $e->getCode(), $e);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete(string $path): void
    {
        try {
            if (!$this->flysystem->fileExists($path)) {
                return;
            }
            $this->flysystem->delete($path);
        } catch (\League\Flysystem\FilesystemException $e) {
            $message = "Can not delete file in path $path: {$e->getMessage()}";
    
            throw new FilesystemException($message, $e->getCode(), $e);
        }
    }
}