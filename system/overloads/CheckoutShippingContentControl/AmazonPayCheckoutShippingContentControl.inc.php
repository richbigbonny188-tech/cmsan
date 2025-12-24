<?php
/* --------------------------------------------------------------
   AmazonPayCheckoutShippingContentControl.inc.php 2017-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AmazonPayCheckoutShippingContentControl extends AmazonPayCheckoutShippingContentControl_parent
{
    public function proceed()
    {
        if(!empty($this->v_data_array['GET']['amazonpay']) && $this->v_data_array['GET']['amazonpay'] === 'stop')
        {
            unset($_SESSION['payment'], $_SESSION['amazonadvpay_order_ref_id'], $_SESSION['amz_loginpay']);
        }
        if(!empty($this->v_data_array['GET']['amazonpay']) && $this->v_data_array['GET']['amazonpay'] === 'start')
        {
            $_SESSION['payment'] = 'amazonadvpay';
        }
        if(!empty($this->v_data_array['POST']['amz-orderrefid']))
        {
            $orderReferenceId = preg_replace('/[^[:alnum:]-]/', '', $this->v_data_array['POST']['amz-orderrefid']);
            $_SESSION['amazonadvpay_order_ref_id'] = $orderReferenceId;
            $_SESSION['amz_loginpay'] = true;
        }
        
        return parent::proceed();
    }
}
