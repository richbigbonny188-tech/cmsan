<?php
/* --------------------------------------------------------------
   Filesystem.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filesystem\Interfaces;

use Gambio\Core\Filesystem\Exceptions\FilesystemException;

/**
 * Interface Filesystem
 *
 * @package Gambio\Core\Filesystem\Interfaces
 * @deprecated
 */
interface Filesystem
{
    /**
     * Returns the base directory.
     *
     * @return string
     *
     * @deprecated
     */
    public function getBaseDirectory(): string;
    
    
    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     *
     * @deprecated
     */
    public function has(string $path): bool;
    
    
    /**
     * Returns content of a file by the provided path.
     *
     * @param string $path Relative path to file.
     *
     * @return string|null Returns null if file doesn't exist.
     *
     * @deprecated
     */
    public function read(string $path): ?string;
    
    
    /**
     * List content of a directory.
     *
     * @param string $directory Relative path to directory.
     * @param bool   $recursive
     *
     * @return DirectoryItems
     *
     * @deprecated
     */
    public function list(string $directory, bool $recursive = false): DirectoryItems;
    
    
    /**
     * Creates a new file with the provided path and content.
     *
     * @param string $path
     * @param string $content
     *
     * @return void
     *
     * @throws FilesystemException If file could not be created or already exists.
     *
     * @deprecated
     */
    public function create(string $path, string $content = ''): void;
    
    
    /**
     * Creates or updates a file with the provided path and content.
     *
     * @param string $path
     * @param string $content
     *
     * @return void
     *
     * @throws FilesystemException If file could not be created/updated.
     *
     * @deprecated
     */
    public function update(string $path, string $content): void;
    
    
    /**
     * Deletes an existing file.
     *
     * @param string $path
     *
     * @return void
     *
     * @throws FilesystemException If file could not be deleted.
     *
     * @deprecated
     */
    public function delete(string $path): void;
}