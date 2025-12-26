<?php
/*--------------------------------------------------------------
   ShoppingCartFactory.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\Collections\SelectedOptions;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\Collections\ShoppingCartItems;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\ItemDetails;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\SelectedOption;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\ShoppingCartItem;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\ShoppingCartItemId;

class ShoppingCartFactory
{
    /**
     * Creates a new instance of Customer ID
     */
    public function createCustomerId(int $id): CustomerId
    {
        return CustomerId::create($id);
    }
    
    
    /**
     * Creates a new instance of Shopping Cart Item ID
     */
    public function createShoppingCartItemId(int $id): ShoppingCartItemId
    {
        return ShoppingCartItemId::create($id);
    }
    
    
    /**
     * Creates a new instance of Product ID
     */
    public function createProductId(string $id): ProductId
    {
        return ProductId::create($id);
    }
    
    
    /**
     * Creates a new instance of Selected Option
     */
    public function createSelectedOption(string $optionKey, string $optionValue): SelectedOption
    {
        return SelectedOption::create($optionKey, $optionValue);
    }
    
    
    /**
     * Creates a new instance of Shopping Cart
     */
    public function createShoppingCartItem(
        string $productId,
        int $shoppingCartItemId,
        string $itemNumber,
        string $title,
        string $image,
        int $amount,
        DateTimeImmutable $addedAt,
        SelectedOptions $selectedOptions
    ): ShoppingCartItem {
        return ShoppingCartItem::create(ProductId::create($productId),
                                        ShoppingCartItemId::create($shoppingCartItemId),
                                        ItemDetails::create($itemNumber, $title, $image),
                                        $amount,
                                        $selectedOptions,
                                        $addedAt);
    }
    
    
    /**
     * Creates a new instance of Shopping Cart Items
     */
    public function createShoppingCartItems(ShoppingCartItem ...$items): ShoppingCartItems
    {
        return ShoppingCartItems::create(...$items);
    }
    
    
    /**
     * Creates a new instance of Selected Options
     */
    public function createSelectedOptions(SelectedOption ...$options): SelectedOptions
    {
        return SelectedOptions::create(...$options);
    }
}