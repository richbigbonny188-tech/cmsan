<?php
/* --------------------------------------------------------------
  ProductImageListUpdateServiceFactoryInterface.php 2020-01-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Interfaces;

use Gambio\ProductImageList\Interfaces\ProductImageListUpdateServiceInterface;

/**
 * Interface ProductImageListUpdateServiceFactoryInterface
 * @package Gambio\ProductImageList\UpdateService\Interfaces
 */
interface ProductImageListUpdateServiceFactoryInterface
{
    /**
     * @return ProductImageListUpdateServiceInterface
     */
    public function service(): ProductImageListUpdateServiceInterface;
}