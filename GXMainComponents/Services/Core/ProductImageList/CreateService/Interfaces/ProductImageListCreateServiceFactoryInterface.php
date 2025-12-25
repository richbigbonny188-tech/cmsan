<?php
/* --------------------------------------------------------------
  ProductImageListCreateServiceFactoryInterface.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\CreateService\Interfaces;

use Gambio\ProductImageList\Interfaces\ProductImageListCreateServiceInterface;

/**
 * Interface ProductImageListCreateServiceFactoryInterface
 * @package Gambio\ProductImageList\CreateService\Interfaces
 */
interface ProductImageListCreateServiceFactoryInterface
{
    /**
     * @return ProductImageListCreateServiceInterface
     */
    public function service(): ProductImageListCreateServiceInterface;
}