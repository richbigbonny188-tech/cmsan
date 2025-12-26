<?php
/* --------------------------------------------------------------
  DisplayTextProvider.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Gambio\Core\TextManager\Services\TextManager;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale\Language;

/**
 * Class DisplayTextProvider
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class DisplayTextProvider
{
    private const SECTION_GENERAL  = 'general';
    private const SECTION_PRODUCTS = 'product_info';


    /**
     * @param TextManager $textManager
     */
    public function __construct(
        private TextManager $textManager
    )
    {
    }


    /**
     * Provides text to indicate that shipping is free.
     *
     * @param Language $language
     *
     * @return string
     */
    public function shippingIsFree(Language $language): string
    {
        return $this->translate('text_free_shipping', self::SECTION_PRODUCTS, $language);
    }


    /**
     * Utility method to pass arguments into the text manager.
     *
     * @param string   $phrase
     * @param string   $section
     * @param Language $language
     *
     * @return string
     */
    private function translate(string $phrase, string $section, Language $language): string
    {
        return $this->textManager->getPhraseText($phrase, $section, $language->id());
    }


    /**
     * Provides text to indicate that shipping has cost.
     *
     * @param Language $language
     *
     * @return string
     */
    public function shippingHasCost(Language $language): string
    {
        return $this->translate('SHIPPING_COSTS', self::SECTION_GENERAL, $language);
    }


    /**
     * Provides text to indicate that shipping is excluded.
     *
     * @param Language $language
     *
     * @return string
     */
    public function shippingIsExcluded(Language $language): string
    {
        return $this->translate('SHIPPING_EXCL', self::SECTION_GENERAL, $language);
    }


    /**
     * Provides text to indicate that taxes are free.
     *
     * @param Language $language
     *
     * @return string
     */
    public function freeTaxes(Language $language): string
    {
        return $this->translate('GM_TAX_FREE', self::SECTION_GENERAL, $language);
    }


    /**
     * Provides text for product included taxes.
     *
     * @param Language $language
     *
     * @return string
     */
    public function includedTaxes(Language $language): string
    {
        return $this->translate('TAX_INFO_INCL', self::SECTION_GENERAL, $language);
    }


    /**
     * Provides text for product added taxes.
     *
     * @param Language $language
     *
     * @return string
     */
    public function addedTaxes(Language $language): string
    {
        return $this->translate('TAX_INFO_ADD', self::SECTION_GENERAL, $language);
    }


    /**
     * Provides text for product excluded taxes.
     *
     * @param Language $language
     *
     * @return string
     */
    public function excludedTaxes(Language $language): string
    {
        return $this->translate('TAX_INFO_EXCL', self::SECTION_GENERAL, $language);
    }


    /**
     * Provides text to be used in add-to-cart action input/image buttons.
     *
     * @param Language $language
     *
     * @return string
     */
    public function addToCart(Language $language): string
    {
        return $this->translate('IMAGE_BUTTON_IN_CART', self::SECTION_GENERAL, $language);
    }
}