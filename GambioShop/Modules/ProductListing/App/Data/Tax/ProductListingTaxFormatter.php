<?php
/*
 * --------------------------------------------------------------
 *   ProductListingTaxFormatter.php 2023-06-14
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2023 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Tax;

use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingTaxFormatter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxFormatter
{
    private const TEXT_SECTION = 'general';
    
    private TextManager                   $textManager;
    private ProductListingTaxFormatPhrase $phrase;
    
    
    /**
     * ProductListingTaxFormatter constructor.
     *
     * @param TextManager                   $textManager
     * @param ProductListingTaxFormatPhrase $phrase
     */
    public function __construct(TextManager $textManager, ProductListingTaxFormatPhrase $phrase)
    {
        $this->textManager = $textManager;
        $this->phrase      = $phrase;
    }
    
    
    /**
     * Formats the tax rate into a translated string.
     *
     * @param float           $taxRate
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function format(float $taxRate, ListingSettings $listingSettings): string
    {
        $phrase = $this->phrase->getPhrase($taxRate, $listingSettings);
        $text   = $this->textManager->getPhraseText($phrase, static::TEXT_SECTION, $listingSettings->languageId());
        
        return sprintf($text, "$taxRate%");
    }
}