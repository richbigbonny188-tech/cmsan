<?php
/* --------------------------------------------------------------
  ModelsFactory.php 2023-03-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Content;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ListingDisplaySettings;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale\Currency;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale\Language;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Seo;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Visibility;

/**
 * Class ModelsFactory
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class ModelsFactory
{
    /**
     * @param Locale     $locale
     * @param Seo        $seo
     * @param Visibility $visibility
     *
     * @return ListingDisplaySettings
     */
    public function createListingDisplaySettings(
        Locale     $locale,
        Seo        $seo,
        Visibility $visibility
    ): ListingDisplaySettings
    {
        return new ListingDisplaySettings($locale, $seo, $visibility);
    }


    /**
     * @param Language $language
     * @param Currency $currency
     *
     * @return Locale
     */
    public function createLocaleSettings(Language $language, Currency $currency): Locale
    {
        return new Locale($language, $currency);
    }


    /**
     * @param int    $id
     * @param string $code
     * @param string $directory
     *
     * @return Language
     */
    public function createLocaleLanguageSettings(int $id, string $code, string $directory): Language
    {
        return new Language($id, $code, $directory);
    }


    /**
     * @param string $code
     * @param string $decimalSeparator
     * @param string $thousandsSeparator
     * @param int    $decimals
     *
     * @return Currency
     */
    public function createLocaleCurrencySettings(
        string $code,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ',',
        int    $decimals = 2
    ): Currency
    {
        return new Currency($code, $decimalSeparator, $thousandsSeparator, $decimals);
    }


    /**
     * @param bool $boostProduct
     * @param bool $boostContent
     * @param bool $boostShortUrls
     * @param bool $useFriendlyUrls
     * @param bool $useBoosterLanguage
     * @param bool $suppressIndexUsage
     *
     * @return Seo
     */
    public function createSeoSettings(
        bool $boostProduct,
        bool $boostContent,
        bool $boostShortUrls,
        bool $useFriendlyUrls,
        bool $useBoosterLanguage,
        bool $suppressIndexUsage
    ): Seo
    {
        return new Seo(
            $boostProduct,
            $boostContent,
            $boostShortUrls,
            $useFriendlyUrls,
            $useBoosterLanguage,
            $suppressIndexUsage
        );
    }


    /**
     * @param int  $thumbnailWidth
     * @param int  $thumbnailHeight
     * @param bool $showPrices
     * @param bool $showShipping
     * @param bool $shippingIsActive
     * @param bool $lightboxIsActive
     *
     * @return Visibility
     */
    public function createVisibilitySettings(
        int  $thumbnailWidth,
        int  $thumbnailHeight,
        bool $showPrices,
        bool $showShipping,
        bool $shippingIsActive,
        bool $lightboxIsActive
    ): Visibility
    {
        return new Visibility(
            $thumbnailWidth,
            $thumbnailHeight,
            $showPrices,
            $showShipping,
            $shippingIsActive,
            $lightboxIsActive
        );
    }


    /**
     * @param int    $contentId
     * @param int    $contentGroupId
     * @param string $contentTitle
     *
     * @return Content
     */
    public function createContent(
        int    $contentId,
        int    $contentGroupId,
        string $contentTitle
    ): Content
    {
        return new Content(
            $contentId,
            $contentGroupId,
            $contentTitle
        );
    }
}