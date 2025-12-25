<?php
/* --------------------------------------------------------------
   VoucherInfoViewHelper.inc.php 2019-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

/**
 * Class VoucherInfoViewHelper
 *
 * Contains code regarding output about voucher which would otherwise have to be duplicated between
 * CheckoutConfirmationContentView and CheckoutConfirmationThemeContentView.
 */
class VoucherInfoViewHelper
{
    /** @var xtcPrice_ORIGIN */
    protected $xtcPrice;
    
    
    public function __construct(xtcPrice_ORIGIN $xtcPrice)
    {
        $this->xtcPrice = $xtcPrice;
    }
    
    
    /**
     * Returns an array with info about how customer’s balance and vouchers are about to be used.
     *
     * The array is aimed at output in the checkout_confirmation.html template file.
     *
     * @return array
     */
    public function getVoucherInfo(): array
    {
        $currencyFactor = (float)$this->xtcPrice->currencies[$this->xtcPrice->actualCurr]['value'];
        
        $voucherInfo = [];
        if (isset($_SESSION['gift_vouchers_amount'])) {
            $gvAmount = $_SESSION['gift_vouchers_amount'];
            if ((bool)$_SESSION['cot_gv'] === true) {
                $gvBalance = $this->getCustomerGVBalance(new IdType((int)$_SESSION['customer_id']));
                $gvBalance *= $currencyFactor;
                
                if ($gvBalance > 0) {
                    $balanceUsed   = min($gvAmount, $gvBalance);
                    $gvAmount      -= $balanceUsed;
                    $voucherInfo[] = [
                        'coupon_code'             => 'balance',
                        'coupon_amount'           => $balanceUsed,
                        'coupon_amount_formatted' => $this->xtcPrice->xtcFormat($balanceUsed, true, 0, false),
                    ];
                }
            }
            if (!empty($_SESSION['gift_vouchers']) && $gvAmount > 0) {
                foreach ($_SESSION['gift_vouchers'] as $giftVoucher) {
                    $voucherCouponId = new IdType((int)$giftVoucher['coupon_id']);
                    $couponDetails   = $this->getCouponDetails($voucherCouponId);
                    if ($couponDetails['coupon_active'] === 'Y') {
                        $voucherAmount = min($gvAmount, $couponDetails['coupon_amount'] * $currencyFactor);
                        $gvAmount      -= $voucherAmount;
                        $voucherInfo[] = [
                            'coupon_code'             => $couponDetails['coupon_code'],
                            'coupon_amount'           => $voucherAmount * $currencyFactor,
                            'coupon_amount_formatted' => $this->xtcPrice->xtcFormat($voucherAmount, true, 0, false),
                        ];
                    }
                }
            }
        }
        
        return $voucherInfo;
    }
    
    
    /**
     * Returns a customer’s current balance from coupons/vouchers.
     *
     * @param \IdType $customerID
     *
     * @return float
     */
    protected function getCustomerGVBalance(IdType $customerID): float
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
    
    
    /**
     * Returns details about a coupon.
     *
     * @param IdType $couponID
     *
     * @return array|null
     */
    protected function getCouponDetails(IdType $couponID): ?array
    {
        $db        = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $couponRow = $db->get_where('coupons', ['coupon_id' => $couponID->asInt()])->row_array();
        
        return $couponRow;
    }
}
