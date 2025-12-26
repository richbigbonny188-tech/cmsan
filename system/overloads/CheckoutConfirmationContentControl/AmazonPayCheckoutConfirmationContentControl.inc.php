<?php
/* --------------------------------------------------------------
   AmazonPayCheckoutConfirmationContentControl.inc.php 2018-11-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AmazonPayCheckoutConfirmationContentControl extends AmazonPayCheckoutConfirmationContentControl_parent
{
	public function proceed()
	{
		if(!empty($this->v_data_array['POST']['amz-orderrefid']))
		{
			$orderReferenceId = preg_replace('/[^[:alnum:]-]/', '', $this->v_data_array['POST']['amz-orderrefid']);
			$_SESSION['amazonadvpay_order_ref_id'] = $orderReferenceId;
			$_SESSION['amz_loginpay'] = true;
		}
		
		return parent::proceed();
	}
	
}
