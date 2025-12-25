<?php
/* --------------------------------------------------------------
  ProductImageListReadServiceFactoryInterface.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ReadService\Interfaces;

use Gambio\ProductImageList\Interfaces\ProductImageListReadServiceInterface;

/**
 * Interface ProductImageListReadServiceFactoryInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface ProductImageListReadServiceFactoryInterface
{
    /**
     * @return ProductImageListReadServiceInterface
     */
    public function service(): ProductImageListReadServiceInterface;
}