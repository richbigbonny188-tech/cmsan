<?php
/**
 * ImageFactoryInterface.php 2020-4-6
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository\Factories;

use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\ImageDto;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Images\ValueObjects\AbstractImageSource;

/**
 * Interface ImageFactoryInterface
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Interfaces
 */
interface ImageFactoryInterface
{
    /**
     * @param ImageDto                 $image
     *
     * @param AbstractImageSource|null $source
     *
     * @return SellingUnitImageInterface
     */
    public function createImage(ImageDto $image, ?AbstractImageSource $source = null): SellingUnitImageInterface;
}