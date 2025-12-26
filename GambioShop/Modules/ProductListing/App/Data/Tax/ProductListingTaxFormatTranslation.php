<?php
/* --------------------------------------------------------------
   ProductListingTaxFormatTranslation.php 2022-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Tax;

/**
 * Class ProductListingTaxFormatTranslation
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxFormatTranslation
{
    private const TEXT_TAX_INCL = 'TAX_INFO_INCL';
    private const TEXT_TAX_ADD  = 'TAX_INFO_ADD';
    private const TEXT_TAX_EXCL = 'TAX_INFO_EXCL';
    private const TEXT_TAX_FREE = 'GM_TAX_FREE';
    
    private bool $isTaxFree;
    private bool $showTax;
    private bool $showEmptyTax;
    private bool $showPriceTax;
    private bool $addTax;
    
    
    /**
     * ProductListingTaxFormatSettings constructor.
     *
     * @param bool $isTaxFree
     * @param bool $showTax
     * @param bool $showEmptyTax
     * @param bool $showPriceTax
     * @param bool $addTax
     */
    public function __construct(bool $isTaxFree, bool $showTax, bool $showEmptyTax, bool $showPriceTax, bool $addTax)
    {
        $this->isTaxFree    = $isTaxFree;
        $this->showTax      = $showTax;
        $this->showEmptyTax = $showEmptyTax;
        $this->showPriceTax = $showPriceTax;
        $this->addTax       = $addTax;
    }
    
    
    /**
     * Returns the text phrase for the formatted tax.
     *
     * @param float $taxRate
     *
     * @return string
     */
    public function getPhrase(float $taxRate): string
    {
        if ($this->isTaxFree) {
            return self::TEXT_TAX_FREE;
        }
        if (!$this->showTax || ($taxRate <= 0 && !$this->showEmptyTax)) {
            return '';
        }
        if ($this->showPriceTax) {
            return self::TEXT_TAX_INCL;
        }
        if ($this->addTax) {
            return self::TEXT_TAX_ADD;
        }
        
        return self::TEXT_TAX_EXCL;
    }
}