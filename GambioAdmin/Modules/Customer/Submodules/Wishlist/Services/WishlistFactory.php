<?php
/*--------------------------------------------------------------
   WishlistFactory.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Collections\SelectedOptions;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Collections\WishlistItems;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\ItemDetails;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\SelectedOption;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\WishlistItem;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\WishlistItemId;

class WishlistFactory
{
    /**
     * Creates an instance of Customer ID
     */
    public function createCustomerId(int $id): CustomerId
    {
        return CustomerId::create($id);
    }
    
    
    /**
     * creates an instance of Wishlist Item ID
     */
    public function createWishlistItemId(int $id): WishlistItemId
    {
        return WishlistItemId::create($id);
    }
    
    
    /**
     * Creates an instance of Product ID
     */
    public function createProductId(string $id): ProductId
    {
        return ProductId::create($id);
    }
    
    
    /**
     * Creates an instance of Selected Options
     */
    public function createSelectedOption(string $optionKey, string $optionValue): SelectedOption
    {
        return SelectedOption::create($optionKey, $optionValue);
    }
    
    
    /**
     * Creates an instance of Wishlist Item
     */
    public function createWishlistItem(
        string $productId,
        int $wishlistItemId,
        string $itemNumber,
        string $title,
        string $image,
        int $amount,
        DateTimeImmutable $addedAt,
        SelectedOptions $selectedOptions
    ): WishlistItem {
        return WishlistItem::create($this->createProductId($productId),
                                    $this->createWishlistItemId($wishlistItemId),
                                    ItemDetails::create($itemNumber, $title, $image),
                                    $amount,
                                    $selectedOptions,
                                    $addedAt);
    }
    
    
    /**
     * creates an instance of Wishlist Items
     */
    public function createWishlistItems(WishlistItem ...$items): WishlistItems
    {
        return WishlistItems::create(...$items);
    }
    
    
    /**
     * Creates an instance of Selected Options
     */
    public function createSelectedOptions(SelectedOption ...$option): SelectedOptions
    {
        return SelectedOptions::create(...$option);
    }
}