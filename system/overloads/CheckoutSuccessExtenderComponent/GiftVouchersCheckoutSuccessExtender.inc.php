<?php
/* --------------------------------------------------------------
   GiftVouchersCheckoutSuccessExtender.inc.php 2019-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GiftVouchersCheckoutSuccessExtender extends GiftVouchersCheckoutSuccessExtender_parent
{
    function proceed()
    {
        parent::proceed();
        $order        = $this->v_data_array['coo_order'];
        $ordersId     = new IdType((int)$order->info['orders_id']);
        $vouchersUsed = $this->getVouchersUsed($ordersId);
        if (!empty($vouchersUsed)) {
            /** @var \xtcPrice_ORIGIN $xtcPrice */
            $xtcPrice   = $GLOBALS['xtPrice'];
            $couponData = [];
            foreach ($vouchersUsed as $voucherUsed) {
                if ($voucherUsed['coupon_code'] === 'balance') {
                    $customerId   = new IdType((int)$order->customer['id']);
                    $gvBalance    = $this->getCustomerGVBalance($customerId);
                    $couponData[] = [
                        'coupon_code'             => 'balance',
                        'coupon_amount_formatted' => $xtcPrice->xtcFormat((float)$gvBalance, true, 0, true),
                    ];
                } else {
                    $couponRow                            = $this->findCouponByCode(
                        new NonEmptyStringType($voucherUsed['coupon_code'])
                    );
                    $couponRow['coupon_amount_formatted'] = $xtcPrice->xtcFormat(
                        (float)$couponRow['coupon_amount'],
                        true,
                        0,
                        true
                    );
                    $couponData[]                         = $couponRow;
                }
            }
            $paymentInstructionContentView = MainFactory::create('GiftVouchersStatusThemeContentView');
            $paymentInstructionContentView->set_content_data('couponData', $couponData);
            $this->html_output_array[] = $paymentInstructionContentView->get_html();
        }
    }
    
    
    protected function getVouchersUsed(IdType $ordersId)
    {
        $db           = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $vouchersUsed = $db->get_where('coupon_gv_redeem_track', ['orders_id' => $ordersId->asInt()])->result_array();
        
        return $vouchersUsed;
    }
    
    
    /**
     * Returns a customer’s current balance from coupons/vouchers.
     *
     * @param \IdType $customerID
     *
     * @return float
     */
    protected function getCustomerGVBalance(IdType $customerID)
    {
        $balance = 0.0;
        if ($customerID->asInt() !== 0) {
            $db            = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $customerGvRow = $db->get_where('coupon_gv_customer', ['customer_id' => $customerID->asInt()])->row_array();
            if (!empty($customerGvRow)) {
                $balance = (float)$customerGvRow['amount'];
            }
        }
        
        return $balance;
    }
    
    
    protected function findCouponByCode(NonEmptyStringType $couponCode)
    {
        $db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $couponData = $db->get_where('coupons', ['coupon_code' => $couponCode->asString()])->row_array();
        
        return $couponData;
    }
}
