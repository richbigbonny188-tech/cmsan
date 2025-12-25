<?php
/* --------------------------------------------------------------
   OptionValue.php 2021-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Entities;

use Gambio\Admin\Modules\Option\Model\Collections\OptionValueDetails;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValuesProductDetails;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueStock;
use Webmozart\Assert\Assert;

/**
 * Class OptionValue
 *
 * @package Gambio\Admin\Modules\Option\Model\Entities
 */
class OptionValue
{
    /**
     * @var OptionValueId
     */
    private $id;
    
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
     * OptionValue constructor.
     *
     * @param OptionValueId              $id
     * @param OptionValueDetails         $details
     * @param OptionValuesProductDetails $productDetails
     * @param OptionValueStock           $stock
     * @param int                        $sortOrder
     * @param string                     $image
     */
    private function __construct(
        OptionValueId $id,
        OptionValueDetails $details,
        OptionValuesProductDetails $productDetails,
        OptionValueStock $stock,
        int $sortOrder,
        string $image
    ) {
        $this->id             = $id;
        $this->details        = $details;
        $this->productDetails = $productDetails;
        $this->stock          = $stock;
        $this->sortOrder      = $sortOrder;
        $this->image          = $image;
    }
    
    
    /**
     * @param OptionValueId              $id
     * @param OptionValueDetails         $details
     * @param OptionValuesProductDetails $productDetails
     * @param OptionValueStock           $stock
     * @param int                        $sortOrder
     * @param string                     $image
     *
     * @return OptionValue
     */
    public static function create(
        OptionValueId $id,
        OptionValueDetails $details,
        OptionValuesProductDetails $productDetails,
        OptionValueStock $stock,
        int $sortOrder,
        string $image
    ): OptionValue {
    
        Assert::greaterThanEq($sortOrder, 0, 'Expected option value sort order greater than or equal to %2$s. Got: %s');
        
        return new self($id, $details, $productDetails, $stock, $sortOrder, $image);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
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
     * @param OptionValueDetails $newDetails
     *
     * @return OptionValue
     */
    public function withDetails(OptionValueDetails $newDetails): OptionValue
    {
        return new self($this->id, $newDetails, $this->productDetails, $this->stock, $this->sortOrder, $this->image);
    }
    
    
    /**
     * @param OptionValuesProductDetails $newProductDetails
     *
     * @return OptionValue
     */
    public function withProductDetails(OptionValuesProductDetails $newProductDetails): OptionValue
    {
        return new self($this->id, $this->details, $newProductDetails, $this->stock, $this->sortOrder, $this->image);
    }
    
    
    /**
     * @param OptionValueStock $newStock
     *
     * @return OptionValue
     */
    public function withStock(OptionValueStock $newStock): OptionValue
    {
        return new self($this->id, $this->details, $this->productDetails, $newStock, $this->sortOrder, $this->image);
    }
    
    
    /**
     * @param int $newSortOrder
     *
     * @return OptionValue
     */
    public function withSortOrder(int $newSortOrder): OptionValue
    {
        Assert::greaterThanEq($newSortOrder, 0, 'Expected option value sort order greater than or equal to %2$s. Got: %s');
        
        return new self($this->id, $this->details, $this->productDetails, $this->stock, $newSortOrder, $this->image);
    }
    
    
    /**
     * @param string $newImage
     *
     * @return OptionValue
     */
    public function withImage(string $newImage): OptionValue
    {
        return new self($this->id, $this->details, $this->productDetails, $this->stock, $this->sortOrder, $newImage);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'                    => $this->id(),
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