<?php
/* --------------------------------------------------------------
  AdaptersFactory.php 2023-05-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\HiddenInput;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ImageInput;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductAddToCartForm;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductImageDisplay;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductQtyInput;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductShippingLink;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductWeight;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ListingDisplaySettings;

/**
 * Class AdaptersFactory
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class AdaptersFactory
{
    /**
     * @param string $shippingUrl
     * @param string $shippingExcludedText
     * @param string $shippingCostText
     * @param string $shippingIsFreeText
     * @param bool   $shippingIsEnabled
     * @param bool   $hasFreeShipping
     *
     * @return ProductShippingLink
     */
    public function createProductShippingLink(
        string $shippingUrl,
        string $shippingExcludedText,
        string $shippingCostText,
        string $shippingIsFreeText,
        bool   $shippingIsEnabled,
        bool   $hasFreeShipping
    ): ProductShippingLink
    {
        return new ProductShippingLink(
            $shippingUrl,
            $shippingExcludedText,
            $shippingCostText,
            $shippingIsFreeText,
            $shippingIsEnabled,
            $hasFreeShipping
        );
    }


    /**
     * @param int    $productId
     * @param string $actionUrl
     *
     * @return ProductAddToCartForm
     */
    public function createProductAddToCartForm(int $productId, string $actionUrl): ProductAddToCartForm
    {
        return new ProductAddToCartForm($productId, $actionUrl);
    }


    /**
     * @param int   $productId
     * @param float $productQty
     *
     * @return ProductQtyInput
     */
    public function createProductQtyInput(int $productId, float $productQty): ProductQtyInput
    {
        return new ProductQtyInput($productId, $productQty);
    }


    /**
     * @param string $name
     * @param string $value
     * @param array  $attributes
     *
     * @return HiddenInput
     */
    public function createHiddenInput(string $name, string $value, array $attributes): HiddenInput
    {
        return new HiddenInput($name, $value, $attributes);
    }


    /**
     * @param string $src
     * @param string $altText
     * @param array  $attributes
     *
     * @return ImageInput
     */
    public function createImageInput(string $src, string $altText = '', array $attributes = []): ImageInput
    {
        return new ImageInput($src, $altText, $attributes);
    }
    
    
    /**
     * @param ?string                $url
     * @param string                 $altText
     * @param int                    $width
     * @param int                    $height
     * @param ListingDisplaySettings $settings
     *
     * @return ProductImageDisplay
     */
    public function createProductImageInfo(
        ?string                $url,
        string                 $altText,
        int                    $width,
        int                    $height,
        ListingDisplaySettings $settings
    ): ProductImageDisplay {
        return new ProductImageDisplay($url,
                                       $altText,
                                       $width,
                                       $height,
                                       $settings->visibility()->thumbnailWidth(),
                                       $settings->visibility()->thumbnailHeight());
    }


    /**
     * @param float                  $weight
     * @param ListingDisplaySettings $settings
     *
     * @return ProductWeight
     */
    public function createProductWeight(
        float                  $weight,
        ListingDisplaySettings $settings
    ): ProductWeight
    {
        return new ProductWeight($weight, $settings->locale());
    }
}