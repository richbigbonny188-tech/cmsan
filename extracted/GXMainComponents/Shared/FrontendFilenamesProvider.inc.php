<?php
/* --------------------------------------------------------------
   FrontendFilenamesProvider.inc.php 2017-05-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FrontendFilenamesProvider
 *
 * @category System
 * @package  Shared
 */
class FrontendFilenamesProvider
{
    /**
     * Returns a list of files supporting forced redirection to urls with language code
     * (used in includes/application_top.php)
     *
     * @return array
     */
    public static function getFilenames()
    {
        return [
            'account.php',
            'account_edit.php',
            'account_history.php',
            'account_history_info.php',
            'account_password.php',
            'address_book.php',
            'address_book_process.php',
            'advanced_search.php',
            'advanced_search_result.php',
            'create_account.php',
            'create_guest_account.php',
            'gm_account_delete.php',
            'gm_price_offer.php',
            'gv_redeem.php',
            'gv_send.php',
            'index.php',
            'login.php',
            'logoff.php',
            'media_content.php',
            'newsletter.php',
            'password_double_opt.php',
            'print_product_info.php',
            'product_info.php',
            'product_reviews.php',
            'product_reviews_write.php',
            'products_new.php',
            'reviews.php',
            'shop.php',
            'shop_content.php',
            'shopping_cart.php',
            'specials.php',
            'wish_list.php',
            'withdrawal.php'
        ];
    }
}