<?php
/* --------------------------------------------------------------
   NewOptionValue.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\ValueObjects;

use Gambio\Admin\Modules\Option\Model\Collections\OptionValueDetails;

/**
 * Class OptionValue
 *
 * @package Gambio\Admin\Modules\Option\Model\ValueObjects
 * @codeCoverageIgnore
 */
class NewOptionValue
{
    /**
     * @var OptionValueDetails
     */
    private $details;
    
    /**
     * @var OptionValuesProductDetails
     */
    private $productDetails;
    
    /**
     * @var OptionValueStock
     */
    private $stock;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    /**
     * @var string
     */
    private $image;
    
    
    /**
     * NewOptionValue constructor.
     *
     * @param OptionValueDetails         $details
     * @param OptionValuesProductDetails $productDetails
     * @param OptionValueStock           $stock
     * @param int                        $sortOrder
     * @param string                     $image
     */
    private function __construct(
        OptionValueDetails $details,
        OptionValuesProductDetails $productDetails,
        OptionValueStock $stock,
        int $sortOrder,
        string $image
    ) {
        $this->details        = $details;
        $this->productDetails = $productDetails;
        $this->stock          = $stock;
        $this->sortOrder      = $sortOrder;
        $this->image          = $image;
    }
    
    
    /**
     * @param OptionValueDetails         $details
     * @param OptionValuesProductDetails $productDetails
     * @param OptionValueStock           $stock
     * @param int                        $sortOrder
     * @param string                     $image
     *
     * @return NewOptionValue
     */
    public static function create(
        OptionValueDetails $details,
        OptionValuesProductDetails $productDetails,
        OptionValueStock $stock,
        int $sortOrder,
        string $image
    ): NewOptionValue {
        return new self($details, $productDetails, $stock, $sortOrder, $image);
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function label(string $languageCode): string
    {
        return $this->details->label($languageCode);
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function description(string $languageCode): string
    {
        return $this->details->description($languageCode);
    }
    
    
    /**
     * @return string
     */
    public function modelNumber(): string
    {
        return $this->productDetails->modelNumber();
    }
    
    
    /**
     * @return float
     */
    public function weight(): float
    {
        return $this->productDetails->weight();
    }
    
    
    /**
     * @return float
     */
    public function price(): float
    {
        return $this->productDetails->price();
    }
    
    
    /**
     * @return string
     */
    public function stockType(): string
    {
        return $this->stock->stockType();
    }
    
    
    /**
     * @return float
     */
    public function stock(): float
    {
        return $this->stock->stock();
    }
    
    
    /**
     * @return bool
     */
    public function isStockCentrallyManaged(): bool
    {
        return $this->stock->isStockCentrallyManaged();
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * @return string
     */
    public function image(): string
    {
        return $this->image;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'sortOrder'             => $this->sortOrder(),
            'image'                 => $this->image(),
            'modelNumber'           => $this->modelNumber(),
            'weight'                => $this->weight(),
            'price'                 => $this->price(),
            'stockType'             => $this->stockType(),
            'stock'                 => $this->stock(),
            'stockCentrallyManaged' => $this->isStockCentrallyManaged(),
            'details'               => $this->details->toArray(),
        ];
    }
}