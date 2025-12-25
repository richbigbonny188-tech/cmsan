<?php
/*--------------------------------------------------------------
   CustomerWishlistMapper.php 2022-11-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\WishlistItem;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Wishlist;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistFactory;

class CustomerWishlistMapper
{
    /**
     * @var WishlistFactory
     */
    private WishlistFactory $factory;
    
    
    /**
     * Constructor
     */
    public function __construct(WishlistFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * Maps the given Data into a Wishlist Item instance
     */
    public function mapWishlistItem(array $wishlistData): WishlistItem
    {
        $selectedOptions = [];
        
        foreach ($wishlistData['selected_options'] as $option) {
            $selectedOptions[] = $this->factory->createSelectedOption($option['option_name'],
                                                                      $option['option_value']);
        }
        
        $itemNumber = [];
        
        if ($wishlistData['products_model'] !== '') {
            $itemNumber[] = $wishlistData['products_model'];
        }
        
        if ($wishlistData['combi_model'] !== '') {
            $itemNumber[] = $wishlistData['combi_model'];
        }
        
        return $this->factory->createWishlistItem($wishlistData['products_id'],
                                                  (int)$wishlistData['customers_basket_id'],
                                                  implode('-', $itemNumber),
                                                  (string)$wishlistData['products_name'],
                                                  (string)$wishlistData['image'],
                                                  (int)$wishlistData['amount'],
                                                  new DateTimeImmutable($wishlistData['added_at'] ?? ''),
                                                  $this->factory->createSelectedOptions(...$selectedOptions));
    }
    
    
    /**
     * Maps the given Data into a Wishlist instance
     */
    public function mapWishlist(array $cartsData, CustomerId $customerId): Wishlist
    {
        $cartItems = [];
        
        foreach ($cartsData as $cart) {
            $cartItems[] = $this->mapWishlistItem($cart);
        }
        
        return Wishlist::create($customerId, $this->factory->createWishlistItems(...$cartItems));
    }
}