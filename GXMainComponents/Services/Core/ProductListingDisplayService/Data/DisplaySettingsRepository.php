<?php
/* --------------------------------------------------------------
  DisplaySettingsRepository.php 2023-12-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Doctrine\DBAL\Exception;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ListingDisplaySettings;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Seo;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Visibility;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class DisplaySettingsRepository
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class DisplaySettingsRepository
{
    /**
     * @param DisplayReader $reader
     * @param ModelsMapper  $mapper
     * @param ModelsFactory $factory
     */
    public function __construct(
        private DisplayReader $reader,
        private ModelsMapper  $mapper,
        private ModelsFactory $factory
    ) {
    }
    
    
    /**
     * Gathers and returns shop information and configurations used to display product listing in the frontend.
     *
     * @param ListingSettings $listingSettings
     *
     * @return ListingDisplaySettings
     * @throws Exception
     */
    public function getListingDisplaySettings(ListingSettings $listingSettings): ListingDisplaySettings
    {
        return $this->factory->createListingDisplaySettings(
            $this->getLocaleSettings($listingSettings),
            $this->getSeoSettings(),
            $this->getVisibilitySettings($listingSettings)
        );
    }
    
    
    /**
     * Returns the locale settings.
     *
     * If the fetched `currency_code` is different from the "$_SESSION['language_code']"
     * - $listingSettings->currencyCode() in this case - the session variable has priority.
     *
     * @param ListingSettings $listingSettings
     *
     * @return Locale
     * @throws Exception
     */
    private function getLocaleSettings(ListingSettings $listingSettings): Locale
    {
        $rawData = $this->reader->fetchLocale($listingSettings->languageId());
        
        if ($rawData['currency_code'] !== $listingSettings->currencyCode()) {
            $currency = $this->getCurrencySettings($listingSettings->currencyCode());
            
            if (!is_null($currency)) {
                $rawData['currency_code']       = $currency->code();
                $rawData['decimal_separator']   = $currency->decimalSeparator();
                $rawData['thousands_separator'] = $currency->thousandsSeparator();
                $rawData['decimals']            = $currency->decimals();
            }
        }
        
        return $this->mapper->mapLocaleSettings($rawData);
    }
    
    
    /**
     * @return Seo
     */
    private function getSeoSettings(): Seo
    {
        return $this->factory->createSeoSettings(
            $this->reader->fetchSeoProductBoostIsEnabled(),
            $this->reader->fetchSeoContentBoostIsEnabled(),
            $this->reader->fetchSeoShortUrlsBoostIsEnabled(),
            $this->reader->fetchSeoUseFriendlyUrls(),
            $this->reader->fetchSeoUseBoostLanguage(),
            $this->reader->fetchSeoSuppressIndexUsage()
        );
    }
    
    
    /**
     * @param ListingSettings $listingSettings
     *
     * @return Visibility
     * @throws Exception
     */
    private function getVisibilitySettings(ListingSettings $listingSettings): Visibility
    {
        return $this->factory->createVisibilitySettings(
            $this->reader->fetchProductThumbnailWidth(),
            $this->reader->fetchProductThumbnailHeight(),
            $this->reader->fetchCanShowPrices($listingSettings),
            $this->reader->fetchShippingIsShown(),
            $this->reader->fetchShippingIsActive(),
            true // this is always true in legacy code
        );
    }
    
    
    /**
     * @param string $currencyCode
     *
     * @return Locale\Currency|null
     */
    private function getCurrencySettings(string $currencyCode): ?Locale\Currency
    {
        $data = $this->reader->fetchCurrencySettingsByCode($currencyCode);
        
        if (is_null($data)) {
            return null;
        }
        
        return $this->factory->createLocaleCurrencySettings(
            $data['currency_code'],
            $data['decimal_separator'],
            $data['thousands_separator'],
            (int)$data['decimals']
        );
    }
}