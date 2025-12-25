<?php
/*--------------------------------------------------------------
   ShoppingCartItem.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\Collections\SelectedOptions;

class ShoppingCartItem
{
    /**
     * @var ProductId
     */
    private ProductId $productId;
    
    /**
     * @var ShoppingCartItemId
     */
    private ShoppingCartItemId $shoppingCartItemId;
    
    /**
     * @var ItemDetails
     */
    private ItemDetails $itemDetails;
    
    /**
     * @var int
     */
    private int $amount;
    
    /**
     * @var SelectedOptions
     */
    private SelectedOptions $selectedOptions;
    
    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $addedAt;
    
    
    /**
     * Constructor
     */
    private function __construct(
        ProductId $productId,
        ShoppingCartItemId $shoppingCartItemId,
        ItemDetails $itemDetails,
        int $amount,
        SelectedOptions $selectedOptions,
        DateTimeImmutable $addedAt
    ) {
        $this->productId          = $productId;
        $this->shoppingCartItemId = $shoppingCartItemId;
        $this->itemDetails        = $itemDetails;
        $this->amount             = $amount;
        $this->selectedOptions    = $selectedOptions;
        $this->addedAt            = $addedAt;
    }
    
    
    /**
     * @param ProductId          $productId
     * @param ShoppingCartItemId $shoppingCartItemId
     * @param ItemDetails        $itemDetails
     * @param int                $amount
     * @param SelectedOptions    $selectedOptions
     * @param DateTimeImmutable  $addedAt
     *
     * @return static
     */
    public static function create(
        ProductId $productId,
        ShoppingCartItemId $shoppingCartItemId,
        ItemDetails $itemDetails,
        int $amount,
        SelectedOptions $selectedOptions,
        DateTimeImmutable $addedAt
    ): self {
        return new self($productId, $shoppingCartItemId, $itemDetails, $amount, $selectedOptions, $addedAt);
    }
    
    
    /**
     * Returns the Product ID
     */
    public function productId(): int
    {
        return $this->productId->value();
    }
    
    
    /**
     * Returns the Shopping Cart Item ID
     */
    public function shoppingCartItemId(): int
    {
        return $this->shoppingCartItemId->value();
    }
    
    
    /**
     * Returns the ItemNumber
     */
    public function itemNumber(): string
    {
        return $this->itemDetails->itemNumber();
    }
    
    
    /**
     * returns the title
     */
    public function title(): string
    {
        return $this->itemDetails->title();
    }
    
    
    /**
     * Returns the image
     */
    public function image(): string
    {
        return $this->itemDetails->image();
    }
    
    
    /**
     * Returns the amount
     */
    public function amount(): int
    {
        return $this->amount;
    }
    
    
    /**
     * Returns the Selected Options
     */
    public function selectedOptions(): SelectedOptions
    {
        return $this->selectedOptions;
    }
    
    
    /**
     * Returns the Added At Date with a given format
     */
    public function addedAt(string $format = "Y-m-d"): string
    {
        return $this->addedAt->format($format);
    }
    
    
    /**
     * Returns an array containing the class members
     */
    public function toArray(string $format = "Y-m-d"): array
    {
        return [
            "productId"          => $this->productId->value(),
            'extendedProductId'  => $this->productId->extended(),
            "shoppingCartItemId" => $this->shoppingCartItemId->value(),
            "itemDetails"        => $this->itemDetails->toArray(),
            "amount"             => $this->amount,
            "selectedOptions"    => $this->selectedOptions->toArray(),
            "addedAt"            => $this->addedAt->format($format),
        ];
    }
}

