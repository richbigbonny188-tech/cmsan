<?php
/*--------------------------------------------------------------
   ProductIdentificationNumbers.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects;

/**
 * Class ProductIdentificationNumbers
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects
 */
class ProductIdentificationNumbers
{
    /**
     * ProductIdentificationNumbers constructor.
     *
     * @param string $modelNumber
     * @param string $ean
     * @param string $gtin
     * @param string $asin
     */
    private function __construct(
        private string $modelNumber,
        private string $ean,
        private string $gtin,
        private string $asin
    ) {
    }
    
    
    /**
     * @param string $modelNumber
     * @param string $ean
     * @param string $gtin
     * @param string $asin
     *
     * @return ProductIdentificationNumbers
     */
    public static function create(
        string $modelNumber,
        string $ean,
        string $gtin,
        string $asin
    ): ProductIdentificationNumbers {
        return new self($modelNumber, $ean, $gtin, $asin);
    }
    
    
    /**
     * @return string
     */
    public function modelNumber(): string
    {
        return $this->modelNumber;
    }
    
    
    /**
     * @return string
     */
    public function ean(): string
    {
        return $this->ean;
    }
    
    
    /**
     * @return string
     */
    public function gtin(): string
    {
        return $this->gtin;
    }
    
    
    /**
     * @return string
     */
    public function asin(): string
    {
        return $this->asin;
    }
}