<?php
/* --------------------------------------------------------------
	PayPal3CheckoutConfirmationThemeContentView.inc.php 2018-12-12
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2018 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This overload fixes a bug in CheckoutConfirmationContentView which prevents display of payment module confirmation info.
 *
 * @todo remove this when the issue regarding PAYMENT_INFORMATION is resolved.
 */
class PayPal3CheckoutConfirmationThemeContentView extends PayPal3CheckoutConfirmationThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        if ($_SESSION['payment'] == 'paypal3') {
            $text = MainFactory::create('PayPalText');
            $this->content_array['PAYMENT_INFORMATION'] = $text->get_text('checkout_confirmation_info');
        }
    }
}
