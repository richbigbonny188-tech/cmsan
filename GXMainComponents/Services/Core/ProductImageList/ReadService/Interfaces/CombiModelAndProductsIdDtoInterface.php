<?php
/* --------------------------------------------------------------
  CombiModelAndProductsIdDtoInterface.php 2020-02-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ReadService\Interfaces;

/**
 * Interface CombiModelAndProductsIdDtoInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface CombiModelAndProductsIdDtoInterface
{
    /**
     * @return string
     */
    public function combiModel(): string;
    
    
    /**
     * @return int
     */
    public function productsId(): int;
}