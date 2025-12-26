<?php
/* --------------------------------------------------------------
   theme_settings.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$themeSettingsArray = [
    'THEME_PRESENTATION_VERSION' => 4.0,
    'MENUBOXES'                  => [
        'add_quickie'             => ['POSITION' => 'gm_box_pos_13', 'STATUS' => 0],
        'admin'                   => ['POSITION' => 'gm_box_pos_3', 'STATUS' => 0],
        'bestsellers'             => ['POSITION' => 'gm_box_pos_4', 'STATUS' => 0],
        'categories'              => ['POSITION' => 'gm_box_pos_1', 'STATUS' => 1],
        'content'                 => ['POSITION' => 'gm_box_pos_5', 'STATUS' => 0],
        'ekomi'                   => ['POSITION' => 'gm_box_pos_21', 'STATUS' => 0],
        'extrabox1'               => ['POSITION' => 'gm_box_pos_23', 'STATUS' => 0],
        'extrabox2'               => ['POSITION' => 'gm_box_pos_24', 'STATUS' => 0],
        'extrabox3'               => ['POSITION' => 'gm_box_pos_25', 'STATUS' => 0],
        'extrabox4'               => ['POSITION' => 'gm_box_pos_26', 'STATUS' => 0],
        'extrabox5'               => ['POSITION' => 'gm_box_pos_27', 'STATUS' => 0],
        'extrabox6'               => ['POSITION' => 'gm_box_pos_28', 'STATUS' => 0],
        'extrabox7'               => ['POSITION' => 'gm_box_pos_29', 'STATUS' => 0],
        'extrabox8'               => ['POSITION' => 'gm_box_pos_30', 'STATUS' => 0],
        'extrabox9'               => ['POSITION' => 'gm_box_pos_31', 'STATUS' => 0],
        'filter'                  => ['POSITION' => 'gm_box_pos_2', 'STATUS' => 1],
        'gm_trusted_shops_widget' => ['POSITION' => 'gm_box_pos_7', 'STATUS' => 0],
        'information'             => ['POSITION' => 'gm_box_pos_19', 'STATUS' => 0],
        'last_viewed'             => ['POSITION' => 'gm_box_pos_15', 'STATUS' => 0],
        'login'                   => ['POSITION' => 'gm_box_pos_16', 'STATUS' => 0],
        'mailbeez_shopvoting'     => ['POSITION' => 'gm_box_pos_22', 'STATUS' => 0],
        'manufacturers'           => ['POSITION' => 'gm_box_pos_10', 'STATUS' => 0],
        'manufacturers_info'      => ['POSITION' => 'gm_box_pos_11', 'STATUS' => 0],
        'newsletter'              => ['POSITION' => 'gm_box_pos_8', 'STATUS' => 0],
        'order_history'           => ['POSITION' => 'gm_box_pos_6', 'STATUS' => 0],
        'paypal'                  => ['POSITION' => 'gm_box_pos_20', 'STATUS' => 0],
        'search'                  => ['POSITION' => 'gm_box_pos_12', 'STATUS' => 0],
        'specials'                => ['POSITION' => 'gm_box_pos_9', 'STATUS' => 0],
        'trusted'                 => ['POSITION' => 'gm_box_pos_17', 'STATUS' => 0],
        'whatsnew'                => ['POSITION' => 'gm_box_pos_14', 'STATUS' => 0],
    ],
    'SETTINGS'                   => [
        'gx-index-full-width'            => true,
        'gx-product-info-details-sticky' => true,
        'gx-product-info-full-width'     => true,
        'gx-product-listing-col-xs'      => 6,
        'gx-product-listing-col-sm'      => 6,
        'gx-product-listing-col-md'      => 4,
        'gx-product-listing-col-lg'      => 3,
        'gx-show-search-top-nav'         => false,
        'gx-header-fixed'                => true,
        'gx-hide-logo-col'               => false,
        'gx-hide-search-col'             => false,
        'gx-hide-custom-1-col'           => false,
        'gx-hide-cart-col'               => false
    ]
];

$themeSettings = MainFactory::create_object('DefaultThemeSettings');
$themeSettings->setThemeSettingsArray($themeSettingsArray);
$themeSettingsArray = $themeSettings->getThemeSettingsArray();
