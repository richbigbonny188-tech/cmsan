<?php
/* --------------------------------------------------------------
  ImageListImageTextDtoInterface.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Interfaces;

/**
 * Interface ImageListImageTextDtoInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface ImageListImageTextDtoInterface
{
    /**
     * @return int
     */
    public function imageId(): int;
    
    
    /**
     * @return string
     */
    public function textType(): string;
    
    
    /**
     * @return string
     */
    public function textValue(): string;
    
    
    /**
     * @return int
     */
    public function languageId(): int;
}