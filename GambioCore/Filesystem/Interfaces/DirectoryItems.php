<?php
/* --------------------------------------------------------------
   DirectoryItems.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filesystem\Interfaces;

use IteratorAggregate;

/**
 * Interface DirectoryItems
 *
 * @package Gambio\Core\Filesystem\Interfaces
 */
interface DirectoryItems extends IteratorAggregate
{
    /**
     * @return DirectoryItem[]
     */
    public function getIterator(): \Traversable;
    
    
    /**
     * Returns internal files as array.
     *
     * @return DirectoryItem[]
     */
    public function items(): array;
}