<?php
/* --------------------------------------------------------------
  ImageListImageDtoInterface.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Interfaces;

/**
 * Interface ImageListImageDtoInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface ImageListImageDtoInterface
{
    /**
     * @return int
     */
    public function imageId(): int;
    
    
    /**
     * @return int
     */
    public function listId(): int;
    
    
    /**
     * @return string
     */
    public function localPath(): string;
    
    
    /**
     * @return int
     */
    public function sortOder(): int;
}