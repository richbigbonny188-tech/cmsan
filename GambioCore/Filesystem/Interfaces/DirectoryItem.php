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

namespace Gambio\Core\Filesystem\Interfaces;

/**
 * Interface DirectoryItem
 *
 * @package Gambio\Core\Filesystem\Interfaces
 */
interface DirectoryItem
{
    /**
     * @return string
     */
    public function absolutePath(): string;
    
    
    /**
     * @return string
     */
    public function basename(): string;
    
    
    /**
     * @return string
     */
    public function dirname(): string;
    
    
    /**
     * @return string
     */
    public function path(): string;
    
    
    /**
     * @return int
     */
    public function timestamp(): int;
    
    
    /**
     * @return bool
     */
    public function isDirectory(): bool;
    
    
    /**
     * @return string|null Returns null if file is a directory.
     */
    public function extension(): ?string;
    
    
    /**
     * @return string|null Returns null if file is a directory.
     */
    public function filename(): ?string;
    
    
    /**
     * @return int|null Returns null if file is a directory.
     */
    public function size(): ?int;
}