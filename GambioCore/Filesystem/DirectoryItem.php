<?php
/* --------------------------------------------------------------
   DirectoryItem.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filesystem;

/**
 * Class DirectoryItem
 *
 * @package Gambio\Core\Filesystem
 */
class DirectoryItem implements Interfaces\DirectoryItem
{
    /**
     * @var string
     */
    private $absolutePath;
    
    /**
     * @var string
     */
    private $basename;
    
    /**
     * @var string
     */
    private $dirname;
    
    /**
     * @var string
     */
    private $path;
    
    /**
     * @var int
     */
    private $timestamp;
    
    /**
     * @var bool
     */
    private $isDirectory;
    
    /**
     * @var string|null
     */
    private $extension;
    
    /**
     * @var string|null
     */
    private $filename;
    
    /**
     * @var int|null
     */
    private $size;
    
    
    /**
     * File constructor.
     *
     * @param string      $absolutePath
     * @param string      $basename
     * @param string      $dirname
     * @param string      $path
     * @param int         $timestamp
     * @param bool        $isDirectory
     * @param string|null $extension
     * @param string|null $filename
     * @param int|null    $size
     */
    private function __construct(
        string $absolutePath,
        string $basename,
        string $dirname,
        string $path,
        int $timestamp,
        bool $isDirectory,
        ?string $extension,
        ?string $filename,
        ?int $size
    ) {
        $this->absolutePath = $absolutePath;
        $this->basename     = $basename;
        $this->dirname      = $dirname;
        $this->path         = $path;
        $this->timestamp    = $timestamp;
        $this->isDirectory  = $isDirectory;
        $this->extension    = $extension;
        $this->filename     = $filename;
        $this->size         = $size;
    }
    
    
    /**
     * @param array  $flysystemData
     * @param string $basePath
     *
     * @return DirectoryItem
     */
    public static function createFromFlysystem(array $flysystemData, string $basePath): DirectoryItem
    {
        $absolutePath = $basePath . '/' . $flysystemData['path'];
        $basename     = $flysystemData['basename'];
        $dirname      = $flysystemData['dirname'];
        $path         = $flysystemData['path'];
        $timestamp    = $flysystemData['timestamp'];
        $isDirectory  = $flysystemData['type'] === 'dir';
        $extension    = ($flysystemData['type'] === 'dir') ? null : $flysystemData['extension'] ?? '';
        $filename     = ($flysystemData['type'] === 'dir') ? null : $flysystemData['filename'];
        $size         = ($flysystemData['type'] === 'dir') ? null : $flysystemData['size'];
        
        return new self($absolutePath,
                        $basename,
                        $dirname,
                        $path,
                        $timestamp,
                        $isDirectory,
                        $extension,
                        $filename,
                        $size);
    }
    
    
    /**
     * @inheritDoc
     */
    public function absolutePath(): string
    {
        return $this->absolutePath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function basename(): string
    {
        return $this->basename;
    }
    
    
    /**
     * @inheritDoc
     */
    public function dirname(): string
    {
        return $this->dirname;
    }
    
    
    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->path;
    }
    
    
    /**
     * @inheritDoc
     */
    public function timestamp(): int
    {
        return $this->timestamp;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isDirectory(): bool
    {
        return $this->isDirectory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function extension(): ?string
    {
        return $this->extension;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filename(): ?string
    {
        return $this->filename;
    }
    
    
    /**
     * @inheritDoc
     */
    public function size(): ?int
    {
        return $this->size;
    }
}