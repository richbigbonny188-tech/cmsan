<?php
/*
 * --------------------------------------------------------------
 *   GambioOmnibusPolicyProductsSwiperThemeContentView.inc.php 2022-05-26
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

/**
 * Class GambioOmnibusPolicyProductsSwiperThemeContentView
 *
 * @package GXModules\Gambio\OmnibusPolicy\Shop\Overloads\ProductsSwiperThemeContentView
 */
class GambioOmnibusPolicyProductsSwiperThemeContentView extends GambioOmnibusPolicyProductsSwiperThemeContentView_parent
{
    use OmnibusPolicyOverloadTrait;

    public function prepare_data()
    {
        parent::prepare_data();

        $textPhrase = null;

        if ($this->moduleIsInstalled()) {
            $textPhrase = $this->reviews()->getReviewsPolicy(
                'content_verified_text_short',
                'product_listing_card_show_badge',
                $_SESSION['language_code'] ?: 'de'
            );
        }

        $this->set_content_data('omnibusPolicyProductListingBadge', $textPhrase);
    }
}