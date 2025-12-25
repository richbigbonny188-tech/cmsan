<?php
/*--------------------------------------------------------------
   WishlistItem.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Collections\SelectedOptions;

class WishlistItem
{
    /**
     * @var ProductId
     */
    private ProductId $productId;
    
    /**
     * @var WishlistItemId
     */
    private WishlistItemId $wishlistItemId;
    
    /**
     * @var ItemDetails
     */
    private ItemDetails $details;
    
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
        WishlistItemId $wishlistItemId,
        ItemDetails $details,
        int $amount,
        SelectedOptions $selectedOptions,
        DateTimeImmutable $addedAt
    ) {
        $this->productId       = $productId;
        $this->wishlistItemId  = $wishlistItemId;
        $this->details         = $details;
        $this->amount          = $amount;
        $this->selectedOptions = $selectedOptions;
        $this->addedAt         = $addedAt;
    }
    
    
    /**
     * Creates a new instance of Wishlist Item.
     */
    public static function create(
        ProductId $productId,
        WishlistItemId $wishlistItemId,
        ItemDetails $details,
        int $amount,
        SelectedOptions $selectedOptions,
        DateTimeImmutable $addedAt
    ): self {
        return new self($productId, $wishlistItemId, $details, $amount, $selectedOptions, $addedAt);
    }
    
    
    /**
     * Returns the Product ID
     */
    public function productId(): int
    {
        return $this->productId->value();
    }
    
    
    /**
     * Returns the Wishlist Item ID
     */
    public function wishlistItemId(): int
    {
        return $this->wishlistItemId->value();
    }
    
    
    /**
     * Returns the Item Number
     */
    public function itemNumber(): string
    {
        return $this->details->itemNumber();
    }
    
    
    /**
     * Returns the Title
     */
    public function title(): string
    {
        return $this->details->title();
    }
    
    
    /**
     * Returns the Image
     */
    public function image(): string
    {
        return $this->details->title();
    }
    
    
    /**
     * Returns the Amount
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
     * Returns the Added Date
     */
    public function addedAt(string $format = "Y-m-d"): string
    {
        return $this->addedAt->format($format);
    }
    
    
    /**
     * Returns the contents of this Value Objects as an array
     */
    public function toArray(string $format = "Y-m-d"): array
    {
        return [
            "productId"         => $this->productId->value(),
            'extendedProductId' => $this->productId->extended(),
            "wishlistItemId"    => $this->wishlistItemId->value(),
            "itemDetails"       => $this->details->toArray(),
            "amount"            => $this->amount,
            "selectedOptions"   => $this->selectedOptions->toArray(),
            "addedAt"           => $this->addedAt->format($format),
        ];
    }
}