<?php
/* --------------------------------------------------------------
   ProductListingModelFactory.php 2023-12-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data;

use DateTime;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemDetailsCodes;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemTaxRates;
use Gambio\Shop\Modules\ProductListing\Model\Entities\ListingItem;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDates;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDetails;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDetailsCode;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDownloadInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImages;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemManufacturer;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemMeta;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemMetaSitemap;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemMetaSitemapFrequency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemMetaSitemapPriority;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceStatus;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemSettingsPricing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemSettingsVisibility;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemSettingsVpe;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShipping;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemStock;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemTax;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemTaxRate;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPaginationMeta;

/**
 * Class ProductListingItemFactory
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingModelFactory
{
    /**
     * Creates a new listing item.
     *
     * @param ListingItemId                $id
     * @param ListingItemDetails           $details
     * @param ListingItemSettings          $settings
     * @param ListingItemShipping          $shipping
     * @param ListingItemStock             $stock
     * @param ListingItemMeta              $meta
     * @param ListingItemTax               $tax
     * @param ListingItemPrice             $price
     * @param ListingItemDates             $dates
     * @param ListingItemImages            $images
     * @param ListingItemManufacturer|null $manufacturer
     *
     * @return ListingItem
     */
    public function createListingItem(
        ListingItemId            $id,
        ListingItemDetails       $details,
        ListingItemSettings      $settings,
        ListingItemShipping      $shipping,
        ListingItemStock         $stock,
        ListingItemMeta          $meta,
        ListingItemTax           $tax,
        ListingItemPrice         $price,
        ListingItemDates         $dates,
        ListingItemImages        $images,
        ?ListingItemManufacturer $manufacturer
    
    ): ListingItem {
        return new ListingItem($id,
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
    }
    
    
    /**
     * Creates a new listing item id.
     *
     * @param int $id
     *
     * @return ListingItemId
     */
    public function createListingItemId(int $id): ListingItemId
    {
        return new ListingItemId($id);
    }
    
    
    /**
     * Creates listing item tax.
     *
     * @param string              $title
     * @param string              $description
     * @param float               $total
     * @param string              $formatted
     * @param ListingItemTaxRates $rates
     *
     * @return ListingItemTax
     */
    public function createListingItemTax(
        string              $title,
        string              $description,
        float               $total,
        string              $formatted,
        ListingItemTaxRates $rates
    ): ListingItemTax {
        return new ListingItemTax($title, $description, $total, $formatted, $rates);
    }
    
    
    /**
     * Creates listing item tax rates.
     *
     * @param ListingItemTaxRate ...$rates
     *
     * @return ListingItemTaxRates
     */
    public function createListingItemTaxRates(ListingItemTaxRate ...$rates): ListingItemTaxRates
    {
        return new ListingItemTaxRates(...$rates);
    }
    
    
    /**
     * Creates listing item tax rate.
     *
     * @param string $description
     * @param float  $rate
     * @param int    $priority
     *
     * @return ListingItemTaxRate
     */
    public function createListingItemTaxRate(
        string $description,
        float  $rate,
        int    $priority
    ): ListingItemTaxRate {
        return new ListingItemTaxRate($description, $rate, $priority);
    }
    
    
    /**
     * Creates listing item details.
     *
     * @param string                  $name
     * @param string                  $description
     * @param string                  $shortDescription
     * @param string                  $model
     * @param ListingItemDetailsCodes $codes
     *
     * @return ListingItemDetails
     */
    public function createListingItemDetails(
        string                  $name,
        string                  $description,
        string                  $shortDescription,
        string                  $model,
        ListingItemDetailsCodes $codes
    ): ListingItemDetails {
        return new ListingItemDetails($name, $description, $shortDescription, $model, $codes);
    }
    
    
    /**
     * Creates listing item settings.
     *
     * @param string                         $type
     * @param float                          $weight
     * @param bool                           $isFsk18
     * @param ListingItemDownloadInformation $downloadInformation
     * @param ListingItemSettingsVpe|null    $vpe
     * @param ListingItemSettingsVisibility  $visibility
     * @param ListingItemSettingsPricing     $pricing
     *
     * @return ListingItemSettings
     */
    public function createListingItemSettings(
        string                         $type,
        float                          $weight,
        bool                           $isFsk18,
        ListingItemDownloadInformation $downloadInformation,
        ?ListingItemSettingsVpe        $vpe,
        ListingItemSettingsVisibility  $visibility,
        ListingItemSettingsPricing     $pricing
    ): ListingItemSettings {
        return new ListingItemSettings($type, $weight, $isFsk18, $downloadInformation, $vpe, $visibility, $pricing);
    }
    
    
    /**
     * Creates listing item visibility settings.
     *
     * @param bool $showStock
     * @param bool $showVpeUnit
     * @param bool $showOnStartPage
     * @param bool $showWeight
     * @param bool $showSitemap
     * @param bool $showReleaseDate
     * @param bool $showFreeShipping
     *
     * @return ListingItemSettingsVisibility
     */
    public function createListingItemSettingsVisibility(
        bool $showStock,
        bool $showVpeUnit,
        bool $showOnStartPage,
        bool $showWeight,
        bool $showSitemap,
        bool $showReleaseDate,
        bool $showFreeShipping
    ): ListingItemSettingsVisibility {
        return new ListingItemSettingsVisibility($showStock,
                                                 $showVpeUnit,
                                                 $showOnStartPage,
                                                 $showWeight,
                                                 $showSitemap,
                                                 $showReleaseDate,
                                                 $showFreeShipping);
    }
    
    
    /**
     * @param ListingItemPriceStatus $priceStatus
     *
     * @return ListingItemSettingsPricing
     */
    public function createListingItemSettingsPricing(
        ListingItemPriceStatus $priceStatus
    ): ListingItemSettingsPricing
    {
        return new ListingItemSettingsPricing(
            $priceStatus->isNormal(),
            $priceStatus->isPriceOnRequest(),
            $priceStatus->isNotAvailableForPurchase()
        );
    }
    
    
    /**
     * Creates listing item packing unit settings.
     *
     * @param float  $vpeValue
     * @param string $vpeUnit
     * @param string $formatted
     *
     * @return ListingItemSettingsVpe
     */
    public function createListingItemSettingsVpe(float $vpeValue, string $vpeUnit, string $formatted): ListingItemSettingsVpe
    {
        return new ListingItemSettingsVpe($vpeValue, $vpeUnit, $formatted);
    }
    
    
    /**
     * Creates listing item stock.
     *
     * @param float       $quantity
     * @param float       $interval
     * @param float       $orderMinQuantity
     * @param string|null $unit
     * @param string|null $availability
     *
     * @return ListingItemStock
     */
    public function createListingItemStock(
        float   $quantity,
        float   $interval,
        float   $orderMinQuantity,
        ?string $unit,
        ?string $availability
    ): ListingItemStock {
        return new ListingItemStock($quantity, $interval, $orderMinQuantity, $unit, $availability);
    }
    
    
    /**
     * Creates a listing item details code.
     *
     * @param string $name
     * @param string $value
     *
     * @return ListingItemDetailsCode
     */
    public function createListingItemDetailsCode(string $name, string $value): ListingItemDetailsCode
    {
        return new ListingItemDetailsCode($name, $value);
    }
    
    
    /**
     * Creates listing item details codes.
     *
     * @param ListingItemDetailsCode ...$codes
     *
     * @return ListingItemDetailsCodes
     */
    public function createListingItemDetailsCodes(ListingItemDetailsCode ...$codes): ListingItemDetailsCodes
    {
        return new ListingItemDetailsCodes(...$codes);
    }
    
    
    /**
     * Creates listing pagination meta data.
     *
     * @param int               $totalItems
     * @param ListingPagination $pagination
     *
     * @return ListingPaginationMeta
     */
    public function createPaginationMeta(int $totalItems, ListingPagination $pagination): ListingPaginationMeta
    {
        $maxPage = (int)ceil($totalItems / $pagination->itemsPerPage());
        if ($maxPage < 1) {
            $maxPage = 1;
        }
        
        return new ListingPaginationMeta($pagination->currentPage(),
                                         $pagination->itemsPerPage(),
                                         $totalItems,
                                         $maxPage);
    }
    
    
    /**
     * Creates listing meta data.
     *
     * @param string                 $title
     * @param string                 $description
     * @param string                 $keywords
     * @param string                 $link
     * @param ListingItemMetaSitemap $sitemap
     *
     * @return ListingItemMeta
     */
    public function createListingItemMeta(
        string                 $title,
        string                 $description,
        string                 $keywords,
        string                 $link,
        ListingItemMetaSitemap $sitemap
    ): ListingItemMeta {
        return new ListingItemMeta($title, $description, $keywords, $link, $sitemap);
    }
    
    
    /**
     * @param string      $createdAt
     * @param string      $modifiedAt
     * @param string|null $availableAt
     * @param string|null $expiresAt
     *
     * @return ListingItemDates
     */
    public function createListingItemDates(
        string  $createdAt,
        string  $modifiedAt,
        ?string $availableAt,
        ?string $expiresAt
    ): ListingItemDates {
        $createDateFromString = static function (?string $string): ?DateTime {
            if ($string && str_starts_with($string, '1000-01-01')) {
                return null;
            }
            
            return $string ? DateTime::createFromFormat('Y-m-d H:i:s', $string) : null;
        };
        
        return new ListingItemDates($createDateFromString($createdAt),
                                    $createDateFromString($modifiedAt),
                                    $createDateFromString($availableAt),
                                    $createDateFromString($expiresAt));
    }
    
    
    /**
     * Creates listing item manufacturer data.
     *
     * @param string      $name
     * @param string      $url
     * @param string|null $image
     *
     * @return ListingItemManufacturer
     */
    public function createListingItemManufacturer(
        string  $name,
        string  $url,
        ?string $image
    ): ListingItemManufacturer {
        return new ListingItemManufacturer($name, $url, $image);
    }
    
    
    /**
     * Creates listing meta sitemap.
     *
     * @param ListingItemMetaSitemapFrequency $frequency
     * @param ListingItemMetaSitemapPriority  $priority
     *
     * @return ListingItemMetaSitemap
     */
    public function createListingItemMetaSitemap(
        ListingItemMetaSitemapFrequency $frequency,
        ListingItemMetaSitemapPriority  $priority
    ): ListingItemMetaSitemap {
        return new ListingItemMetaSitemap($frequency, $priority);
    }
    
    
    /**
     * Creates listing meta sitemap frequency.
     *
     * @param string $frequency
     *
     * @return ListingItemMetaSitemapFrequency
     */
    public function createListingItemMetaSitemapFrequency(string $frequency): ListingItemMetaSitemapFrequency
    {
        return new ListingItemMetaSitemapFrequency($frequency);
    }
    
    
    /**
     * Creates listing meta sitemap priority.
     *
     * @param float $priority
     *
     * @return ListingItemMetaSitemapPriority
     */
    public function createListingItemMetaSitemapPriority(float $priority): ListingItemMetaSitemapPriority
    {
        return new ListingItemMetaSitemapPriority($priority);
    }
    
    
    /**
     * Creates listing item price status from the given flag.
     *
     * The flag should be a value between 0 and 2, representing the different price status.
     * If another value is used, the ListingItemPriceStatus uses as fallback the normal price status (flag 0).
     *
     * @param int $statusFlag
     *
     * @return ListingItemPriceStatus
     * @see ListingItemPriceStatus
     */
    public function createListingItemPriceStatus(int $statusFlag): ListingItemPriceStatus
    {
        return new ListingItemPriceStatus($statusFlag);
    }
    
    
    /**
     * Creates listing item price information from the arguments provided.
     *
     * @param float                  $basePrice
     * @param float                  $taxRate
     * @param ListingItemPriceStatus $priceStatus
     *
     * @return \Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation
     */
    public function createListingItemPriceInformation(
        float                  $basePrice,
        float                  $taxRate,
        ListingItemPriceStatus $priceStatus
    ): ListingItemPriceInformation {
        return new ListingItemPriceInformation($basePrice, $taxRate, $priceStatus);
    }
}
