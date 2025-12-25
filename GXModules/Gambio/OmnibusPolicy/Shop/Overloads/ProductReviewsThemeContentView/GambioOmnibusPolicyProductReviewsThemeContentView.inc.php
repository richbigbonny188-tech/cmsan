<?php
/*
 * --------------------------------------------------------------
 *   GambioOmnibusPolicyProductReviewsThemeContentView.inc.php 2022-05-25
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

/**
 * Class GambioOmnibusPolicyProductReviewsThemeContentView
 *
 * @package GXModules\Gambio\OmnibusPolicy\Shop\Overloads\ProductReviewsThemeContentView
 */
class GambioOmnibusPolicyProductReviewsThemeContentView extends GambioOmnibusPolicyProductReviewsThemeContentView_parent
{
    use OmnibusPolicyOverloadTrait;

    public function prepare_data()
    {
        parent::prepare_data();

        $textPhraseBefore = $textPhraseAfter = null;

        if ($this->moduleIsInstalled()) {
            $languageCode     = $_SESSION['language_code'] ?: 'de';
            $textPhraseBefore = $this->reviews()->getReviewsPolicy(
                'content_verified_text',
                'product_info_page_show_text_before',
                $languageCode
            );
            $textPhraseAfter  = $this->reviews()->getReviewsPolicy(
                'content_verified_text',
                'product_info_page_show_text_after',
                $languageCode
            );
        }

        $this->set_content_data('omnibusPolicyProductInfoTextBefore', $textPhraseBefore);
        $this->set_content_data('omnibusPolicyProductInfoTextAfter', $textPhraseAfter);
    }
}