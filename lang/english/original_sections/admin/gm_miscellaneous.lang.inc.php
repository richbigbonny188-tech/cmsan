<?php
/* --------------------------------------------------------------
	gm_miscellaneous.lang.inc.php 2021-11-02
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2021 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array(
	'ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING_INFO'   => '
	<p>
        Technical information means for example:
        <ul>
            <li>the version info of your shop</li>
            <li>installed modules and updates, active languages, active countries etc.</li>
            <li>server information (e.g. PHP and mySQL version, settings, loaded modules)</li>
        </ul>
    </p>
    <p>
        The shared information does not contain any personal or trade-related data. You can disable the sharing of technical information at any time via "Shop Settings" in the Gambio Admin.
    </p>
    <div>
        Thank you for helping us to make Gambio even better!
    </div>',
    'ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING_TEXT' => 'Share technical information of my shop installation with Gambio',
    'BUTTON_EXECUTE'                                            => 'Execute',
    'BUTTON_SAVE'                                               => 'Save',
    'GM_CAT_STOCK'                                              => 'Show stock in <strong>all</strong> categories?',
    'GM_CAT_STOCK_SUCCESS'                                      => 'Categories were successfully updated.',
    'GM_DELETE_IMAGES'                                          => 'Delete all original product images permanently?',
    'GM_DELETE_IMAGES_ADVICE_1'                                 => ' file could not be deleted, because the script user does not have sufficient rights.',
    'GM_DELETE_IMAGES_ADVICE_2'                                 => ' files could not be deleted, because the script user does not have sufficient rights.',
    'GM_DELETE_IMAGES_MESSAGE_1'                                => '',
    'GM_DELETE_IMAGES_MESSAGE_2'                                => ' of ',
    'GM_DELETE_IMAGES_MESSAGE_3'                                => ' files were successfully deleted.',
    'GM_DELETE_IMAGES_TITLE'                                    => 'Delete Original Product Images',
    'GM_DELETE_UNUSED_IMAGES'                                   => 'Delete all unused product images permanently?',
    'GM_DELETE_UNUSED_IMAGES_MESSAGE_1'                         => '',
    'GM_DELETE_UNUSED_IMAGES_MESSAGE_2'                         => ' of ',
    'GM_DELETE_UNUSED_IMAGES_MESSAGE_3'                         => ' files were successfully deleted from the images/product_images/... directories.',
    'GM_MISCELLANEOUS_SUCCESS'                                  => 'Changes were successfully updated.',
    'GM_ORDER_STATUS_CANCEL_ID'                                 => 'ID in the MySQL table "orders_status" for the order status of the cancelation. This ID should only be modified if the new ID is known or the ID has not been saved. The default value here is "99"',
    'GM_PRODUCT_STOCK'                                          => 'Show stock in <strong>all</strong> products?',
    'GM_PRODUCT_STOCK_SUCCESS'                                  => 'Products were successfully updated.',
    'GM_TAX_FREE'                                               => 'VAT Exempt as per Current Regulations',
    'GM_TELL_A_FRIEND'                                          => 'Activate "Ask Product Question" module?',
    'GM_TITLE_STAT'                                             => 'Order Status',
    'GM_TITLE_STATS'                                            => 'Delete Statistics',
    'GM_TITLE_STOCK'                                            => 'Show Stock',
    'GM_TRUNCATE_FLYOVER'                                       => 'Truncate products short description in "flyover" after X characters.',
    'GM_TRUNCATE_FLYOVER_TEXT'                                  => 'Truncate product name in "flyover" after X characters.',
    'GM_TRUNCATE_PRODUCTS_HISTORY'                              => 'Truncate product name in menu box ordering information after X characters.',
    'GM_TRUNCATE_PRODUCTS_NAME'                                 => 'Truncate product name on start page after X characters.',
    'GRADUATED_ASSIGN'                                          => 'Do not disable the sum up of the article stock while the price calculation.',
    'GRADUATED_ASSIGN_INFO'                                     => 'If deactivated, same GX Customizer products will not be grouped together and no graduated prices will be applied to them.',
    'HEADING_TITLE'                                             => 'Miscellaneous',
    'PRODUCT_REVIEW_NAME'                                       => 'Type of Name Displaying for New Product Reviews',
    'PRODUCT_REVIEW_NAME_OPTION_SHORT_FIRSTNAME'                => 'Short firstname',
    'PRODUCT_REVIEW_NAME_OPTION_SHORT_LASTNAME'                 => 'Short lastname',
    'PRODUCT_REVIEW_NAME_OPTION_SHORT_NOTHING'                  => 'Full name',
    'SHOW_OLD_DISCOUNT_PRICE_TEXT'                              => 'Show Standard Price for Discounted Prices',
    'SHOW_OLD_GROUP_PRICE_TEXT'                                 => 'Show Standard Price for Customer Group\'s and Graduated Prices',
    'SHOW_OLD_SPECIAL_PRICE_INFO'                               => 'By activating this option, the stored article price is shown as recommended retail price (RRP). Please note that this price has to be an actual, valid recommended retail price. If the price doesn\'t constitute a recommended retail price, but is, e.g., a former shop-price, the label "RRP" has to be altered. You can alter the label under "Edit Text". To avoid any legal problems, we recommend taking professional advice.',
    'SHOW_OLD_SPECIAL_PRICE_TEXT'                               => 'Show RRP (Recommended Retail Price) for Specials',
    'SHOW_NORMAL_PRICE_ONLY_FOR_NATIONAL_SHIPPING_TEXT'         => 'Show Normal Price Only for National Shipping',
    'SHOW_NORMAL_PRICE_ONLY_FOR_NATIONAL_SHIPPING_INFO'         => 'By activating this option, the normal price / RRP will be displayed only if the delivery takes place in the own country.',
    'TITLE_STAT_EXTERN_KEWORDS'                                 => 'External Keywords',
    'TITLE_STAT_IMPRESSIONS'                                    => 'Page Impressions',
    'TITLE_STAT_INTERN_KEWORDS'                                 => 'Internal Keywords',
    'TITLE_STAT_PRODUCTS_PURCHASED'                             => 'Products Purchased',
    'TITLE_STAT_PRODUCTS_VIEWED'                                => 'Products Searched',
    'TITLE_STAT_USER_INFO'                                      => 'User Info',
    'TITLE_STAT_VISTORS'                                        => 'Visitors',
    'MANUAL_ORDER_STANDARD_PAYMENT_NAME'                        => 'Standard Payment Method for Manually Created Orders',
    'MANUAL_ORDER_STANDARD_PAYMENT_INFO'                        => 'If your desired payment method is not available, you can active it under <b><a href="../admin/admin.php?do=HubConfiguration/PaymentMethods">Payment Systems</a></b>.',
    'DISPLAY_VPE_PRODUCT_OPTIONS_TEXT'                          => 'Show VPE for additional options',
    'DISPLAY_VPE_PRODUCT_OPTIONS_INFO'                          => 'Attention! Creating and editing VPEs/basic prices in additional options will no longer be possible from shop version GX 4.7 onwards. Basic prices will have to be managed in the root products or the product variants. Please make sure that you no longer have any VPEs in the additional options when you update to GX 4.7 or higher.',
);