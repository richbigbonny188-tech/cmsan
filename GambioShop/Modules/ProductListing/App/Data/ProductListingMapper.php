<?php
/* --------------------------------------------------------------
   ProductListingMapper.php 2023-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data;

use Gambio\Shop\Modules\ProductListing\App\Data\Currency\ProductListingCurrencyRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Downloads\ProductListingDownloadRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\GroupSettings\ProductListingGroupSettingsRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Images\ProductListingImagesRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPriceRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Shipping\ProductListingShippingRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Vpe\ProductListingVpeRepository;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CurrencyNotFoundException;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CustomerGroupSettingsNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemDetailsCodes;
use Gambio\Shop\Modules\ProductListing\Model\Entities\ListingItem;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemGroupSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDates;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDetails;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImages;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemManufacturer;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemMeta;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceStatus;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemSettingsVpe;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShipping;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemStock;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemTax;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingMapper
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingMapper
{
    /**
     * ProductListingMapper constructor.
     *
     * @param ProductListingModelFactory            $factory
     * @param ProductListingShippingRepository      $shippingRepository
     * @param ProductListingTaxRepository           $taxRepository
     * @param ProductListingPriceRepository         $priceRepository
     * @param ProductListingImagesRepository        $imagesRepository
     * @param ProductListingGroupSettingsRepository $groupSettingsRepository
     * @param ProductListingCurrencyRepository      $currencyRepository
     * @param ProductListingDownloadRepository      $downloadRepository
     * @param ProductListingVpeRepository           $vpeRepository
     */
    public function __construct(
        private ProductListingModelFactory            $factory,
        private ProductListingShippingRepository      $shippingRepository,
        private ProductListingTaxRepository           $taxRepository,
        private ProductListingPriceRepository         $priceRepository,
        private ProductListingImagesRepository        $imagesRepository,
        private ProductListingGroupSettingsRepository $groupSettingsRepository,
        private ProductListingCurrencyRepository      $currencyRepository,
        private ProductListingDownloadRepository      $downloadRepository,
        private ProductListingVpeRepository           $vpeRepository
    ) {
    }
    
    
    /**
     * Maps a list of raw product data into a list of product listing items.
     *
     * @param array           $productsRawData
     * @param ListingSettings $listingSettings
     *
     * @return array
     * @throws CustomerGroupSettingsNotFoundException|CurrencyNotFoundException
     */
    public function map(array $productsRawData, ListingSettings $listingSettings): array
    {
        $groupSettings = $this->groupSettingsRepository->getGroupSettings($listingSettings);
        $currency      = $this->currencyRepository->getCurrency($listingSettings->currencyCode());
        
        $cb = function (array $data) use ($listingSettings, $currency, $groupSettings): ListingItem {
            $id           = $this->factory->createListingItemId((int)$data['id']);
            $priceStatus  = $this->factory->createListingItemPriceStatus((int)$data['priceStatus']);
            $details      = $this->createDetails($data);
            $shipping     = $this->createShipping($id, $listingSettings, $data);
            $stock        = $this->createStock($data);
            $meta         = $this->createMeta($data);
            $tax          = $this->createTax($data, $listingSettings);
            $price        = $this->createPrice($data,
                                               $id,
                                               $priceStatus,
                                               $tax,
                                               $currency,
                                               $groupSettings,
                                               $listingSettings);
            $settings     = $this->createSettings($data, $priceStatus, $price, $listingSettings);
            $dates        = $this->createDates($data);
            $images       = $this->createImages($id, $listingSettings);
            $manufacturer = $this->createManufacturer($data);
            
            return $this->factory->createListingItem($id,
                                                     $details,
                                                     $settings,
                                                     $shipping,
                                                     $stock,
                                                     $meta,
                                                     $tax,
                                                     $price,
                                                     $dates,
                                                     $images,
                                                     $manufacturer);
        };
        
        return array_map($cb, $productsRawData);
    }
    
    
    /**
     * Maps from $data and creates and instance of ListingItemDetails.
     *
     * @param array $data
     *
     * @return ListingItemDetails
     */
    private function createDetails(array $data): ListingItemDetails
    {
        $name             = $data['name'];
        $description      = $data['description'];
        $shortDescription = $data['shortDescription'];
        $model            = $data['model'];
        $codes            = $this->mapCodes($data);
        
        return $this->factory->createListingItemDetails($name, $description, $shortDescription, $model, $codes);
    }
    
    
    /**
     * Maps from $data and creates an instance of ListingItemSettings.
     *
     * @param array                  $data
     * @param ListingItemPriceStatus $priceStatus
     * @param ListingItemPrice       $price
     * @param ListingSettings        $listingSettings
     *
     * @return ListingItemSettings
     */
    private function createSettings(
        array                  $data,
        ListingItemPriceStatus $priceStatus,
        ListingItemPrice       $price,
        ListingSettings        $listingSettings
    ): ListingItemSettings {
        $type             = $data['type'];
        $weight           = (float)$data['weight'];
        $isFsk18          = (bool)$data['fsk18'];
        $showStock        = (bool)$data['showStock'];
        $showVpe          = (bool)$data['showVpe'];
        $showStartPage    = (bool)$data['showStartPage'];
        $showWeight       = (bool)$data['showWeight'];
        $showSiteMap      = (bool)$data['showSiteMap'];
        $showRelease      = (bool)$data['showRelease'];
        $showFreeShipping = (bool)$data['isDownloadable'];
        
        $visibility = $this->factory->createListingItemSettingsVisibility($showStock,
                                                                          $showVpe,
                                                                          $showStartPage,
                                                                          $showWeight,
                                                                          $showSiteMap,
                                                                          $showRelease,
                                                                          $showFreeShipping);
        $pricing    = $this->factory->createListingItemSettingsPricing($priceStatus);
        
        $downloadInformation = $this->downloadRepository->getDownloadInformation($this->factory->createListingItemId((int)$data['id']));
        $vpe                 = $showVpe ? $this->createListingItemSettingsVpe($data, $price, $listingSettings) : null;
        
        return $this->factory->createListingItemSettings($type,
                                                         $weight,
                                                         $isFsk18,
                                                         $downloadInformation,
                                                         $vpe,
                                                         $visibility,
                                                         $pricing);
    }
    
    
    /**
     * @param ListingItemId   $productId
     * @param ListingSettings $settings
     * @param array           $data
     *
     * @return ListingItemShipping
     */
    private function createShipping(
        ListingItemId   $productId,
        ListingSettings $settings,
        array           $data
    ): ListingItemShipping {
        $useVariantsShippingTime = (bool)$data['useVariantsShippingTime'];
        $hasVariants             = (bool)$data['hasVariants'];
        $withRange               = $useVariantsShippingTime && $hasVariants;
        
        return $this->shippingRepository->getListingItemShipping($productId, $settings, $withRange);
    }
    
    
    /**
     * Maps from $data and creates an instance of ListingItemStock.
     *
     * @param array $data
     *
     * @return ListingItemStock
     */
    private function createStock(array $data): ListingItemStock
    {
        $quantity         = (float)$data['quantity'];
        $interval         = (float)$data['quantityInterval'];
        $orderMinQuantity = (float)$data['orderMinQuantity'];
        $unit             = $data['quantityUnit'] ? : '';
        $availability     = $data['availability'] ? : '';
        
        return $this->factory->createListingItemStock($quantity, $interval, $orderMinQuantity, $unit, $availability);
    }
    
    
    /**
     * Maps from $data and creates an instance of ListingItemMeta.
     *
     * @param array $data
     *
     * @return ListingItemMeta
     */
    private function createMeta(array $data): ListingItemMeta
    {
        $title       = $data['metaTitle'];
        $description = $data['metaDescription'];
        $keywords    = $data['metaKeywords'];
        $url         = $data['productUrl'];
        
        $frequency = $this->factory->createListingItemMetaSitemapFrequency($data['metaSitemapFrequency']);
        $priority  = $this->factory->createListingItemMetaSitemapPriority((float)$data['metaSitemapPriority']);
        $sitemap   = $this->factory->createListingItemMetaSitemap($frequency, $priority);
        
        return $this->factory->createListingItemMeta($title, $description, $keywords, $url ?? '', $sitemap);
    }
    
    
    /**
     * Passes the mapped base price, the newly created ListingPriceStatus and ListingItemTax
     * into the price repository to get the listing item price.
     *
     * @param array                    $data
     * @param ListingItemId            $id
     * @param ListingItemPriceStatus   $priceStatus
     * @param ListingItemTax           $tax
     * @param ListingItemCurrency      $currency
     * @param ListingItemGroupSettings $groupSettings
     * @param ListingSettings          $listingSettings
     *
     * @return ListingItemPrice
     */
    public function createPrice(
        array                    $data,
        ListingItemId            $id,
        ListingItemPriceStatus   $priceStatus,
        ListingItemTax           $tax,
        ListingItemCurrency      $currency,
        ListingItemGroupSettings $groupSettings,
        ListingSettings          $listingSettings
    ): ListingItemPrice {
        $basePrice        = (float)$data['price'];
        $priceInformation = $this->factory->createListingItemPriceInformation($basePrice, $tax->total(), $priceStatus);
        
        return $this->priceRepository->getPrice($id, $priceInformation, $listingSettings, $groupSettings, $currency);
    }
    
    
    /**
     * Passes the mapped tax class id into the tax repository in order to get the listing item tax.
     *
     * @param array           $data
     * @param ListingSettings $listingSettings
     *
     * @return ListingItemTax
     */
    private function createTax(array $data, ListingSettings $listingSettings): ListingItemTax
    {
        return $this->taxRepository->getListingItemTax((int)$data['taxClassId'], $listingSettings);
    }
    
    
    /**
     * Maps product related dates information into an instance of ListingItemDates
     *
     * @param array $data
     *
     * @return ListingItemDates
     */
    private function createDates(array $data): ListingItemDates
    {
        $createdAt   = $data['createdAt'];
        $modifiedAt  = $data['modifiedAt'];
        $availableAt = empty($data['availableAt']) ? null : $data['availableAt'];
        $expiresAt   = empty($data['expiresAt']) ? null : $data['expiresAt'];
        
        return $this->factory->createListingItemDates($createdAt, $modifiedAt, $availableAt, $expiresAt);
    }
    
    
    /**
     * Maps product tagged images with different types into an instance of ListingItemImages
     *
     * @param ListingItemId   $productId
     * @param ListingSettings $settings
     *
     * @return ListingItemImages
     */
    private function createImages(ListingItemId $productId, ListingSettings $settings): ListingItemImages
    {
        return $this->imagesRepository->getListingItemImages($productId, $settings);
    }
    
    
    /**
     * Maps Manufacturer information into an instance of ListingItemManufacturer
     *
     * @param array $data
     *
     * @return ListingItemManufacturer|null
     */
    private function createManufacturer(array $data): ?ListingItemManufacturer
    {
        if (empty($data['manufacturerName'])) {
            return null;
        }
        
        $name  = $data['manufacturerName'];
        $url   = $data['manufacturerUrl'] ?? '';
        $image = $data['manufacturerImage'] ?? '';
        
        return $this->factory->createListingItemManufacturer($name, $url, $image);
    }
    
    
    /**
     * Maps ISBN, UPC, MPN and JAN codes into a collection.
     *
     * @param array $data
     *
     * @return ListingItemDetailsCodes
     */
    private function mapCodes(array $data): ListingItemDetailsCodes
    {
        $isbn = $this->factory->createListingItemDetailsCode('isbn', $data['isbn']);
        $upc  = $this->factory->createListingItemDetailsCode('upc', $data['upc']);
        $mpn  = $this->factory->createListingItemDetailsCode('mpn', $data['mpn']);
        $jan  = $this->factory->createListingItemDetailsCode('jan', $data['jan']);
        
        return $this->factory->createListingItemDetailsCodes($isbn, $upc, $mpn, $jan);
    }
    
    
    /**
     * @param array                      $data
     * @param ListingItemPrice           $price
     * @param ListingSettings            $listingSettings
     *
     * @return ListingItemSettingsVpe|null
     */
    private function createListingItemSettingsVpe(
        array            $data,
        ListingItemPrice $price,
        ListingSettings  $listingSettings
    ): ?ListingItemSettingsVpe {
        $hasVariants = (bool)$data['hasVariants'];
        $vpeUnit     = $data['vpeUnit'];
        $vpeValue    = (float)$data['vpeValue'];
        
        if (!$hasVariants && $vpeUnit && $vpeValue !== 0.0) {
            try {
                $currency = $this->currencyRepository->getCurrency($listingSettings->currencyCode());
            } catch (CurrencyNotFoundException) {
                return null;
            }
            $formatted = $this->vpeRepository->getFormatted($listingSettings, $price, $currency, $vpeUnit, $vpeValue);
            
            return $this->factory->createListingItemSettingsVpe($vpeValue, $vpeUnit, $formatted);
        }
        
        return null;
    }
}