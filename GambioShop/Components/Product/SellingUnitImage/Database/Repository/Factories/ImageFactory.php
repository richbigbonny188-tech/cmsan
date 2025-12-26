<?php
/**
 * ImageFactory.php 2020-06-02
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository\Factories;

use Gambio\Shop\SellingUnit\Database\Configurations\ShopPaths;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\ImageDto;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Images\Entities\SellingUnitImage;
use Gambio\Shop\SellingUnit\Images\ValueObjects\AbstractImageSource;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageAlternateText;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageNumber;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImagePath;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ImageUrl;
use Gambio\Shop\SellingUnit\Images\ValueObjects\ProductImageSource;

/**
 * Class ImageFactory
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Factories
 */
class ImageFactory implements ImageFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createImage(ImageDto $image, ?AbstractImageSource $source = null): SellingUnitImageInterface
    {
        return new SellingUnitImage(new ImageUrl($image->relativePath()),
                                    new ImagePath($image->relativePath()),
                                    new ImageAlternateText($image->alternateText()),
                                    new ImageNumber($image->number()),
                                    new ImageUrl($image->infoUrl()),
                                    new ImageUrl($image->popUpUrl()),
                                    new ImageUrl($image->thumbNailUrl()),
                                    new ImageUrl($image->galleryUrl()),
                                    $source ?? new ProductImageSource
        );
    }
}