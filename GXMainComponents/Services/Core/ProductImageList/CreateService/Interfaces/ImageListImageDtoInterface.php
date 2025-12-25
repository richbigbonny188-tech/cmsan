<?php
/**
 * ImageListImageDtoInterface.php 2020-2-5
 * Last Modified: 2/5/20, 12:09 PM
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\ProductImageList\CreateService\Interfaces;

interface ImageListImageDtoInterface
{
    /**
     * @return int
     */
    public function listId(): int;
    
    
    /**
     * @return string
     */
    public function localPath(): string;
}