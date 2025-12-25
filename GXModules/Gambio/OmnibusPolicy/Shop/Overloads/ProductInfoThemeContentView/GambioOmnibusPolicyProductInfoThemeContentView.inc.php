<?php
/*
 * --------------------------------------------------------------
 *   GambioOmnibusPolicyProductInfoThemeContentView.inc.php 2022-05-25
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

/**
 * Class GambioOmnibusPolicyProductInfoThemeContentView
 *
 * @package GXModules\Gambio\OmnibusPolicy\Shop\Overloads\ProductInfoThemeContentView
 */
class GambioOmnibusPolicyProductInfoThemeContentView extends GambioOmnibusPolicyProductInfoThemeContentView_parent
{
    use OmnibusPolicyOverloadTrait;

    public function prepare_data()
    {
        parent::prepare_data();

        $productListingTextPhrase = $productInfoBadge = null;

        if ($this->moduleIsInstalled()) {
            $productListingTextPhrase = $this->reviews()->getReviewsPolicy(
                'content_verified_text_short',
                'product_listing_card_show_badge',
                $_SESSION['language_code'] ?: 'de'
            );
            $productInfoBadge         = $this->reviews()->reviewsBadgeIsEnabled('product_info_page_show_badge');
        }

        $this->set_content_data('omnibusPolicyProductListingShortText', $productListingTextPhrase);
        $this->set_content_data('omnibusPolicyProductListingBadge', $productInfoBadge);

    }
}