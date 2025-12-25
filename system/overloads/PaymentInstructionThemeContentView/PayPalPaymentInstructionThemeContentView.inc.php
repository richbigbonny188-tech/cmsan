<?php
/* --------------------------------------------------------------
	PayPalPaymentInstructionThemeContentView.inc.php 2018-12-12
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2018 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PayPalPaymentInstructionThemeContentView extends PayPalPaymentInstructionThemeContentView_parent
{
    protected function _getPaymentInstruction($orders_id)
    {
        $paymentInstruction = parent::_getPaymentInstruction($orders_id);

        if ($paymentInstruction !== null && $this->_getPaymentMethod($orders_id) === 'paypal3') {
            $this->set_content_template('checkout_payment_instruction_paypal_pui.html');
            if ($paymentInstruction['due_date'] !== '1000-01-01') {
                $paypalText = MainFactory::create('PayPalText');
                $paymentInstruction['additional_note'] = $paypalText->get_text('pui_why_paypal') . '<br><br>';
                $paymentInstruction['additional_note'] .= COMPANY_NAME . ' ' . $paypalText->get_text('payment_instruction_additional_note');
            }
        }

        return $paymentInstruction;
    }

}