<?php
/* --------------------------------------------------------------
   GiftVouchersService.inc.php 2020-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

/**
 * Class GiftVouchersService
 *
 */
class GiftVouchersService
{
    protected $giftVouchersConfiguration;
    
    
    public function __construct(GiftVouchersConfigurationStorage $configuration)
    {
        $this->giftVouchersConfiguration = $configuration;
    }
    
    
    public function createQueueEntry(
        float $amount,
        IdType $customerId = null,
        IdType $orderId = null,
        IdType $ordersProductsId = null
    ): IdType {
        $queueData = [
            'customer_id'        => $customerId !== null ? $customerId->asInt() : 0,
            'order_id'           => $orderId !== null ? $orderId->asInt() : 0,
            'orders_products_id' => $ordersProductsId !== null ? $ordersProductsId->asInt() : null,
            'amount'             => $amount,
        ];
        $db        = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->insert('coupon_gv_queue', $queueData);
        $uniqueId = $db->insert_id();
        
        return new IdType((int)$uniqueId);
    }
    
    /**
     * @param IdType $orderId
     *
     * @return array
     * @throws Exception
     */
    public function getUnreleasedQueueEntriesByOrder(IdType $orderId): array
    {
        $db          = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $voucherRows = $db->get_where('coupon_gv_queue',
                                      ['order_id' => $orderId->asInt(), 'release_flag' => 'N'])->result_array();
        return $voucherRows;
    }
    
    
    /**
     * @param IdType           $uniqueId
     *
     * @param DecimalType|null $amount
     *
     * @return CouponEntity
     * @throws InvalidGiftVouchersQueueIdException
     */
    public function releaseQueueEntry(IdType $uniqueId, ?DecimalType $amount = null): CouponEntity
    {
        $db       = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $queueRow = $db->get_where('coupon_gv_queue', ['unique_id' => $uniqueId->asInt()])->row_array();
        if ($queueRow === null) {
            throw new InvalidGiftVouchersQueueIdException('queue id not found: ' . $uniqueId->asInt());
        }
        if ($queueRow['release_flag'] === 'Y') {
            throw new InvalidGiftVouchersQueueIdException('queue entry already released: ' . $uniqueId->asInt());
        }
        
        if ($amount !== null) {
            $couponAmount = $amount;
        } else {
            $couponAmount = new DecimalType((float)$queueRow['amount']);
        }
        
        /** @var CouponEntity $coupon */
        $coupon = MainFactory::create('CouponEntity');
        $coupon->setCouponAmount($couponAmount);
        $coupon->setCouponType(new NonEmptyStringType('G'));
        $coupon->setCouponCode(new NonEmptyStringType($this->createVoucherCode()));
        $coupon->setCouponStartDate(new DateTime());
        /** @var CouponRepository $couponRepository */
        $couponRepository = MainFactory::create('CouponRepository', StaticGXCoreLoader::getDatabaseQueryBuilder());
        $couponRepository->storeCoupon($coupon);
        
        $db->update('coupon_gv_queue', ['release_flag' => 'Y'], ['unique_id' => $uniqueId->asInt()]);
        
        if (!empty($queueRow['order_id']) && !empty($queueRow['orders_products_id'])) {
            $gvMailText = MainFactory::create('LanguageTextManager', 'gv_mail');
            $orderId = new IdType((int)$queueRow['order_id']);
            $comment = new StringType($gvMailText->get_text('TEXT_CREATED_VOUCHER_WITH_CODE') . ' '
                                      . $coupon->getCouponCode()->asString());
            /** @var OrderWriteService $orderWrite */
            $orderWrite = StaticGXCoreLoader::getService('OrderWrite');
            $orderWrite->addOrderStatusHistoryEntry($orderId, $comment, new IdType((int)$_SESSION['customer_id']));
            $db->insert('orders_products_to_coupons',
                        [
                            'orders_products_id' => (int)$queueRow['orders_products_id'],
                            'coupon_id'          => $coupon->getCouponId()->asInt()
                        ]);
        }
        
        return $coupon;
    }
    
    
    public function createVoucherCode(): string
    {
        $codeLength = $this->giftVouchersConfiguration->get('securityCodeLength');
        /** @var EntropyProvider $entropyProvider */
        $entropyProvider = MainFactory::create('EntropyProvider');
        $randomData      = $entropyProvider->randomBytes(new IntType((int)ceil($codeLength / 2)), new BoolType(true));
        $voucherCode     = substr(bin2hex($randomData), 0, $codeLength);
        
        return $voucherCode;
    }
    
    
    public function createGiftVoucher(DecimalType $amount)
    {
        /** @var CouponEntity $coupon */
        $coupon = MainFactory::create('CouponEntity');
        $coupon->setCouponAmount($amount);
        $coupon->setCouponType(new NonEmptyStringType('G'));
        $coupon->setCouponCode(new NonEmptyStringType($this->createVoucherCode()));
        $coupon->setCouponStartDate(new DateTime());
        /** @var CouponRepository $couponRepository */
        $couponRepository = MainFactory::create('CouponRepository', StaticGXCoreLoader::getDatabaseQueryBuilder());
        $couponRepository->storeCoupon($coupon);
        
        return $coupon;
    }
    
    
    /**
     * @param string $couponCode
     *
     * @return float
     * @throws InvalidCouponCodeException
     */
    public function getAmountByCouponCode(string $couponCode)
    {
        $coupon = $this->getCouponByCouponCode($couponCode);
        
        return (float)$coupon['coupon_amount'];
    }
    
    
    /**
     * @param string $couponCode
     *
     * @return mixed
     * @throws InvalidCouponCodeException
     */
    public function getCouponByCouponCode(string $couponCode)
    {
        $db        = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $couponRow = $db->get_where('coupons', ['coupon_code' => $couponCode])->row_array();
        if (empty($couponRow)) {
            throw new InvalidCouponCodeException('coupon code does not match a known coupon');
        }
        
        return $couponRow;
    }
}
