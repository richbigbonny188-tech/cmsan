<?php
/* --------------------------------------------------------------
  DisplayMapper.php 2023-12-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Exception;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ListingDisplaySettings;

/**
 * Class DisplayMapper
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class DisplayMapper
{
    private const SHIPPING_STATUS_IMAGES_PATH = 'images/icons/status/';
    
    
    /**
     * @param DisplayRepository $repository
     * @param bool|null         $customerCantPurchaseFsk18
     */
    public function __construct(
        private DisplayRepository $repository,
        private ?bool             $customerCantPurchaseFsk18 = null
    ) {
        if ($this->customerCantPurchaseFsk18 === null) {
            $this->customerCantPurchaseFsk18 = $_SESSION['customers_status']['customers_fsk18_purchasable'] === '0';
        }
    }
    
    
    /**
     * @param array                  $rawData
     * @param ListingDisplaySettings $settings
     *
     * @return array
     * @throws Exception
     */
    public function mapOutput(array $rawData, ListingDisplaySettings $settings): array
    {
        /**
         * @param string        $key
         * @param callable|null $callback
         * @param bool          $isMandatory
         *
         * @return array|mixed|string
         * @throws Exception
         */
        $mapItemData = function (
            string    $key,
            ?callable $callback = null,
            bool      $isMandatory = false
        ) use ($rawData) {
            $value = $this->findIn($key, $rawData, $callback);
            
            if (empty($value) && true === $isMandatory) {
                throw new Exception(sprintf('Missing or empty mandatory product listing item data for "%s"',
                                            $key));
            }
            
            return $value;
        };
        
        $price = $mapItemData('price/special/formatted');
        
        if ($price === null || $price === '') {
            $price = $mapItemData('price/formatted');
        }
        
        $productPriceStatusIsNormal                  = $this->prepareBoolValue($mapItemData('settings/pricing/isNormal'));
        $productPriceStatusIsPriceOnRequest          = $this->prepareBoolValue($mapItemData('settings/pricing/isPriceAvailableOnRequest'));
        $productPriceStatusIsNotAvailableForPurchase = $this->prepareBoolValue($mapItemData('settings/pricing/isNotAvailableForPurchase'));
        
        // Product
        $productId                  = $mapItemData('id', null, true);
        $productName                = $mapItemData('details/name', [$this, 'prepareHtmlOutput']);
        $productLink                = $this->repository->getProductLink(
            $productId,
            $productName,
            $settings
        );
        $productImage               = $this->repository->getProductImage(
            $settings,
            $mapItemData('images/main/url'),
            $mapItemData('images/main/alt')
        );
        
        // Shipping
        $productHasFreeShipping    = $this->prepareBoolValue($mapItemData('settings/visibility/showFreeShipping'));
        $productShippingLinkActive = $this->repository->getProductShippingLinkActive(
            $mapItemData('shipping/linkIsActive'),
            $productPriceStatusIsNormal,
            $settings
        );
        $productShippingLink       = $this->repository->getProductShippingLink(
            $productPriceStatusIsNormal,
            $productPriceStatusIsNotAvailableForPurchase,
            $productHasFreeShipping,
            $settings
        );
        $productShippingName       = $this->repository->getProductShippingName(
            $mapItemData('shipping/name'),
            $productPriceStatusIsNormal,
            $settings
        );
        $productShippingImage      = $this->repository->getProductShippingImage(
            $mapItemData('shipping/image', [$this, 'mapShippingStatusImage']),
            $productPriceStatusIsNormal,
            $settings
        );
        $productAddToCartForm = $this->repository->getProductAddToCartForm($productId, $settings);
        
        // Product quantity
        $productQtyInfo      = $this->repository->getProductQtyInput(
            $productId,
            $mapItemData('stock/orderMinQuantity') ? : 0
        );
        $productQtyInfoArray = $productPriceStatusIsNormal ? $productQtyInfo->toArray() : [];
        $productQtyInfoHtml  = $productPriceStatusIsNormal ? $productQtyInfo->toHtml() : '';
        
        $productVpe     = $productPriceStatusIsPriceOnRequest ? '' : $mapItemData('settings/vpe/formatted');
        $productTaxInfo = $productPriceStatusIsPriceOnRequest ? '' : $mapItemData('tax/formatted');
        $productWeight  = $this->repository->getProductWeight(
            $mapItemData('settings/weight') ? : 0,
            $settings
        );
        
        $buyNowUrl    = $this->repository->getProductBuyNowUrl(
            $productId,
            $productPriceStatusIsNormal,
            $settings
        );
        $buyNowButton = $this->mapProductBuyNowButton(
            $productId,
            $productPriceStatusIsNormal,
            $rawData['settings']['isFsk18'],
            $settings
        );
        
        // Resetting some parameters which the actual `product_ORIGIN::buildDataArray`
        // returns an empty string in some special cases
        if ($this->customerCantPurchaseFsk18 && $rawData['settings']['isFsk18']) {
            $productQtyInfoArray = [];
            $buyNowUrl           = $productQtyInfoHtml = '';
        }
        
        $mapped = [
            'PRODUCTS_ID'                    => (string)$productId,
            'PRODUCTS_NAME'                  => $productName,
            'PRODUCTS_DESCRIPTION'           => $mapItemData('details/description'),
            'PRODUCTS_SHORT_DESCRIPTION'     => $mapItemData('details/shortDescription'),
            'PRODUCTS_PRICE'                 => $price,
            'PRODUCTS_MODEL'                 => $mapItemData('details/model', [$this, 'prepareHtmlOutput']),
            'PRODUCTS_SHIPPING_NAME'         => $productShippingName,
            'PRODUCTS_SHIPPING_RANGE'        => $mapItemData('shipping/range', [$this, 'mapProductShippingRange']),
            'PRODUCTS_SHIPPING_IMAGE'        => $productShippingImage,
            'PRODUCTS_VPE'                   => $productVpe,
            'PRODUCTS_LINK'                  => $productLink,
            'PRODUCTS_TAX_INFO'              => $productTaxInfo,
            'PRODUCTS_SHIPPING_LINK'         => $productShippingLink->toHtml(),
            'PRODUCTS_SHIPPING_LINK_ACTIVE'  => $productShippingLinkActive,
            'GM_PRODUCTS_BUTTON_BUY_NOW_URL' => $buyNowUrl,
            'GM_PRODUCTS_BUTTON_BUY_NOW'     => $buyNowButton,
            'PRODUCTS_EXPIRES'               => $mapItemData('dates/expiresAt'),
            'PRODUCTS_FSK18'                 => $mapItemData('settings/isFsk18') ? "1" : "0",
            'GM_FORM_ACTION'                 => $productAddToCartForm->toHtml(),
            'FORM_DATA'                      => $productAddToCartForm->toArray(),
            'QTY_DATA'                       => $productQtyInfoArray,
            'GM_PRODUCTS_QTY'                => $productQtyInfoHtml,
            'GM_PRODUCTS_STOCK'              => (string)$mapItemData('stock/quantity'),
            'PRODUCTS_META_DESCRIPTION'      => $mapItemData('meta/description'),
            'PRODUCTS_WEIGHT'                => $productWeight->toHtml(),
            'SHOW_PRODUCTS_WEIGHT'           => $mapItemData('settings/visibility/showWeight') ? "1" : "0",
            /**
             * append static data
             */
            'COUNT'                          => '',
            'PRODUCTS_BUTTON_BUY_NOW'        => '', // deprecated since GX 4.5
            'GM_FORM_END'                    => '</form>',
            'PRODUCTS_CATEGORY_URL'          => '', // because ProductListing does not support categories yet.
        ];
        
        return array_merge($mapped, $productImage->toArray());
    }
    
    
    /**
     * @param string        $path
     * @param array         $array
     * @param callable|null $callback
     *
     * @return array|mixed|string
     */
    private function findIn(string $path, array &$array, ?callable $callback): mixed
    {
        $parents = explode('/', $path);
        
        $ref = &$array;
        
        foreach ($parents as $parent) {
            if (is_array($ref) && array_key_exists($parent, $ref)) {
                $ref = &$ref[$parent];
            } else {
                return '';
            }
        }
        
        if ($callback) {
            return $callback($ref);
        }
        
        return $ref;
    }
    
    
    /**
     * @param $value
     *
     * @return bool
     */
    private function prepareBoolValue($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    
    
    /**
     * @param int                    $productId
     * @param bool                   $priceStatusIsNormal
     * @param bool                   $isFsk18
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    private function mapProductBuyNowButton(
        int                    $productId,
        bool                   $priceStatusIsNormal,
        bool                   $isFsk18,
        ListingDisplaySettings $settings
    ): string {
        if (!$settings->visibility()->showPrices()) {
            return '';
        }
        
        $input = $this->repository->getProductBuyNowButtonInput($productId);
        if (false === $priceStatusIsNormal || ($this->customerCantPurchaseFsk18 && $isFsk18)) {
            return $input->toHtml();
        }
        
        $image = $this->repository->getProductBuyNowButtonImage($settings);
        
        return $input->toHtml() . $image->toHtml();
    }
    
    
    /**
     * @param array|null $value
     *
     * @return array|null
     */
    private function mapProductShippingRange(?array $value): ?array
    {
        $ranges = [];
        
        if (!$value) {
            return null;
        }
        
        foreach ($value as $key => $range) {
            if (!empty($range['image'])) {
                $range['image'] = $this->mapShippingStatusImage($range['image']);
            }
            
            if (!empty($range['days']) && is_numeric($range['days'])) {
                $range['days'] = (string)$range['days'];
            }
            
            $ranges[$key] = $range;
        }
        
        return $ranges;
    }
    
    
    /**
     * @param $value
     *
     * @return string
     */
    private function mapShippingStatusImage($value): string
    {
        return self::SHIPPING_STATUS_IMAGES_PATH . $value;
    }
    
    
    /**
     * @param $value
     *
     * @return string
     */
    private function prepareHtmlOutput($value): string
    {
        $value    = strval($value);
        $encoding = preg_match('//u', $value) ? 'UTF-8' : 'ISO-8859-1';
        
        return htmlspecialchars($value, ENT_COMPAT, $encoding);
    }
}