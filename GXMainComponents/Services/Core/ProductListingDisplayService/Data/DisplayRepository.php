<?php
/* --------------------------------------------------------------
  DisplayRepository.php 2023-12-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Doctrine\DBAL\Exception;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\HtmlOutput;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductAddToCartForm;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductImageDisplay;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters\ProductQtyInput;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ListingDisplaySettings;
use StyleEditServiceFactory;

/**
 * Class DisplayRepository
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class DisplayRepository
{
    private const PATH_PUBLIC_THEME   = 'public/theme';
    private const PATH_IMAGES_BUTTONS = 'images/buttons';


    /**
     * @param DisplayReader         $reader
     * @param DisplayTextProvider   $textProvider
     * @param DisplayUrlsRepository $urls
     * @param ModelsMapper          $mapper
     * @param AdaptersFactory       $adaptersFactory
     * @param Path                  $path
     */
    public function __construct(
        private DisplayReader         $reader,
        private DisplayTextProvider   $textProvider,
        private DisplayUrlsRepository $urls,
        private ModelsMapper          $mapper,
        private AdaptersFactory       $adaptersFactory,
        private Path                  $path
    )
    {
    }


    /**
     * Generates product details page URL.
     * Return value is affected by SEO boos enable/disable status.
     *
     * @param int                    $productId
     * @param string                 $productName
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    public function getProductLink(
        int                    $productId,
        string                 $productName,
        ListingDisplaySettings $settings
    ): string
    {
        return $this->urls->getProductLink($productId, $productName, $settings);
    }
    
    
    /**
     * Provides an HTML output adapter for product shipping info and link.
     * Return value would be an empty string '' if:
     *      - showing shipping information is disabled in the configurations
     *      - product is not sellable, means products.gm_price_status !== '0'
     *
     * @param bool                   $productPriceStatusIsNormal
     * @param bool                   $productPriceStatusIsNotAvailableForPurchase
     * @param bool                   $productHasFreeShipping
     * @param ListingDisplaySettings $settings
     *
     * @return HtmlOutput
     */
    public function getProductShippingLink(
        bool                   $productPriceStatusIsNormal,
        bool                   $productPriceStatusIsNotAvailableForPurchase,
        bool                   $productHasFreeShipping,
        ListingDisplaySettings $settings
    ): HtmlOutput
    {
        $shippingIsEnabled    = $settings->visibility()->showShipping() &&
                                ($productPriceStatusIsNormal || $productPriceStatusIsNotAvailableForPurchase);
        $shippingUrl          = '';
        $shippingExcludedText = '';
        $shippingCostText     = '';
        $shippingIsFreeText   = '';

        if ($shippingIsEnabled) {
            $language = $settings->locale()->language();
            if ($productHasFreeShipping) {
                $shippingIsFreeText = $this->textProvider->shippingIsFree($language);
            } else {
                $shippingUrl          = $this->getContentPopupLink($settings);
                $shippingExcludedText = $this->textProvider->shippingIsExcluded($language);
                $shippingCostText     = $this->textProvider->shippingHasCost($language);
            }
        }

        return $this->adaptersFactory->createProductShippingLink(
            $shippingUrl,
            $shippingExcludedText,
            $shippingCostText,
            $shippingIsFreeText,
            $shippingIsEnabled,
            $productHasFreeShipping
        );
    }
    
    
    /**
     * Returns the shipping name if the shipping is active
     *
     * @param string                 $value
     * @param bool                   $productPriceStatusIsNormal
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    public function getProductShippingName(string $value, bool $productPriceStatusIsNormal, ListingDisplaySettings $settings): string
    {
        if (!$productPriceStatusIsNormal ||
            !$settings->visibility()->shippingIsActive() ||
            !$settings->visibility()->showShipping()
        ) {
            return '';
        }
        
        return $value;
    }
    
    
    /**
     * Returns the shipping name if the shipping is active
     *
     * @param string                 $value
     * @param bool                   $productPriceStatusIsNormal
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    public function getProductShippingImage(string $value, bool $productPriceStatusIsNormal, ListingDisplaySettings $settings): string
    {
        if (!$productPriceStatusIsNormal ||
            !$settings->visibility()->shippingIsActive() ||
            !$settings->visibility()->showShipping()
        ) {
            return '';
        }
        
        return $value;
    }
    
    
    /**
     * Returns the shipping name if the shipping is active
     *
     * @param bool                   $value
     * @param bool                   $productPriceStatusIsNormal
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    public function getProductShippingLinkActive(bool $value, bool $productPriceStatusIsNormal, ListingDisplaySettings $settings): string
    {
        if (!$productPriceStatusIsNormal ||
            !$settings->visibility()->shippingIsActive() ||
            !$settings->visibility()->showShipping()
        ) {
            return '';
        }
        
        return $value ? "1" : "0";
    }


    /**
     * Gets shipping information content popup link for frontend display.
     *
     * @param ListingDisplaySettings $settings
     *
     * @return string
     * @throws Exception
     */
    private function getContentPopupLink(ListingDisplaySettings $settings): string
    {
        $language       = $settings->locale()->language();
        $contentGroupId = $this->reader->fetchShippingInfoContentGroupId();
        $contentData    = $this->reader->fetchContent($contentGroupId, $language->id());
        if ($contentData === null) {
            return '';
        }
        $content        = $this->mapper->mapContent($contentData);

        return $this->urls->getContentLink($content, $settings);
    }


    /**
     * Gets product image adapter with product main image url & alt text.
     * If no image was passed, the default product standard image thumbnail will be returned.
     *
     * @param ListingDisplaySettings $settings
     * @param string            $imageUrl
     * @param string                 $imageAltText
     *
     * @return ProductImageDisplay
     */
    public function getProductImage(
        ListingDisplaySettings $settings,
        ?string                $imageUrl = '',
        string                 $imageAltText = ''
    ): ProductImageDisplay
    {
        $imagePath = !empty($imageUrl) ? $imageUrl : 'images/product_images/thumbnail_images/noimage.gif';
        $absolute  = $this->path->base() . DIRECTORY_SEPARATOR . $imagePath;

        [$width, $height] = $this->readDimensionsFromDatabase($imagePath);

        if ($width === 0 && $height === 0 && file_exists($absolute)) {
            [$width, $height] = @getimagesize($absolute);
        }
        
        $url = empty($imageUrl) ? null : $imagePath;

        return $this->adaptersFactory->createProductImageInfo($url, $imageAltText, $width, $height, $settings);
    }


    /**
     * @param string $url
     *
     * @return array
     */
    private function readDimensionsFromDatabase(string $url): array
    {
        return $this->reader->fetchImageDimensions($this->getImageDbName($url));
    }


    /**
     * @param string $url
     *
     * @return string
     */
    private function getImageDbName(string $url): string
    {
        $search = [
            "images/product_images/thumbnail_images/",
            "images/product_images/gallery_images/",
            "images/product_images/info_images/",
            "images/product_images/option_images/",
            "images/product_images/original_images/",
            "images/product_images/popup_images/",
            "images/product_images/properties_combis_images/",
        ];

        return str_replace($search, '', $url);
    }


    /**
     * Provides add-to-cart form as an output adapter with correct action value generated by product ID.
     *
     * @param int                    $productId
     * @param ListingDisplaySettings $settings
     *
     * @return ProductAddToCartForm
     */
    public function getProductAddToCartForm(
        int                    $productId,
        ListingDisplaySettings $settings
    ): ProductAddToCartForm
    {
        $seo       = $settings->seo();
        $routeName = 'index.php';
        $params    = ['BUYproducts_id' => $productId];

        if (true === $seo->useBoosterLanguage() && true === $seo->suppressIndexUsage()) {
            $routeName = "{$settings->locale()->language()->code()}/$routeName";
        }

        if (isset($_GET['cat'])) {
            // append only if is in category page
            $params['cat'] = $_GET['cat'];
        }
        
        if (isset($_GET['keywords'])) {
            $params['keywords'] = $_GET['keywords'];
        }

        $actionUrl = $this->urls->getAction(
            'buy_now',
            $routeName,
            $params
        );

        return $this->adaptersFactory->createProductAddToCartForm($productId, $actionUrl);
    }


    /**
     * Provides a multiple output adapter for product qty input.
     *
     * @param int   $productId
     * @param float $productQty
     *
     * @return ProductQtyInput
     */
    public function getProductQtyInput(int $productId, float $productQty): ProductQtyInput
    {
        return $this->adaptersFactory->createProductQtyInput($productId, $productQty);
    }


    /**
     * Generates products buy_now URL.
     * Returns empty string '' if:
     *      - showing prices is disabled for customer (customers_status_show_price)
     *      - product is not sellable, means products.gm_price_status !== '0'
     *
     * @param int                    $productId
     * @param bool                   $productPriceStatusIsNormal
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    public function getProductBuyNowUrl(
        int                    $productId,
        bool                   $productPriceStatusIsNormal,
        ListingDisplaySettings $settings
    ): string
    {
        if (!$settings->visibility()->showPrices() || !$productPriceStatusIsNormal) {
            return '';
        }

        $seo       = $settings->seo();
        $params    = [
                'BUYproducts_id' => $productId,
            ] + $_GET;
        $routeName = $_SERVER['SCRIPT_NAME'];
        $routeName = basename($routeName);

        if (true === $seo->useBoosterLanguage() && true === $seo->suppressIndexUsage()) {
            $routeName = "{$settings->locale()->language()->code()}/" . trim($routeName, '/');
        }

        return $this->urls->getAction('buy_now', $routeName, $params, true);
    }


    /**
     * Provides an HTML output adapter for input that holds product buy_now info.
     *
     * @param int $productId
     *
     * @return HtmlOutput
     */
    public function getProductBuyNowButtonInput(int $productId): HtmlOutput
    {
        return $this->adaptersFactory->createHiddenInput(
            'products_id',
            (string)$productId,
            ['class' => 'gm_products_id']
        );
    }


    /**
     * Provides an HTML output adapter for image input for add-to-cart image button.
     *
     * @param ListingDisplaySettings $settings
     *
     * @return HtmlOutput
     */
    public function getProductBuyNowButtonImage(
        ListingDisplaySettings $settings
    ): HtmlOutput
    {
        $language = $settings->locale()->language();

        return $this->adaptersFactory->createImageInput(
            implode('/', [
                $this->getThemePath(),
                self::PATH_IMAGES_BUTTONS,
                $language->directory(),
                'button_in_cart.gif',
            ]),
            $this->textProvider->addToCart($language),
            ['class' => 'gm_image_button']
        );
    }
    
    
    /**
     * @return string
     */
    public function getThemePath(): string
    {
        $isEditing = StyleEditServiceFactory::service()->isEditing();
        
        return $isEditing ? StyleEditServiceFactory::service()->getPublishedThemePath() : static::PATH_PUBLIC_THEME;
    }


    /**
     * Provides an HTML output adapter for formatted product weight.
     *
     * @param float                  $weight
     * @param ListingDisplaySettings $settings
     *
     * @return HtmlOutput
     */
    public function getProductWeight(float $weight, ListingDisplaySettings $settings): HtmlOutput
    {
        return $this->adaptersFactory->createProductWeight($weight, $settings);
    }
}