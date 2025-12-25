<?php
/* --------------------------------------------------------------
   CouponRepository.inc.php 2020-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class CouponRepository
{
    /** @var CI_DB_query_builder */
    protected $queryBuilder;
    
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @param IdType $couponId
     *
     * @return CouponEntity
     * @throws InvalidCouponIdException
     */
    public function getCouponById(IdType $couponId): CouponEntity
    {
        $couponRow = $this->queryBuilder->get_where('coupons', ['coupon_id' => $couponId->asInt()])->row_array();
        if ($couponRow === null) {
            throw new InvalidCouponIdException('coupon id ' . $couponId->asInt() . ' is not valid');
        }
        $coupon = $this->createCouponEntityFromDatabaseRow($couponRow);
        return $coupon;
    }
    
    
    /**
     * @param string $couponCode
     *
     * @return CouponEntity
     * @throws InvalidCouponCodeException
     */
    public function getCouponByCode(string $couponCode): CouponEntity
    {
        $couponRow = $this->queryBuilder->get_where('coupons', ['coupon_code' => $couponCode])->row_array();
        if ($couponRow === null) {
            throw new InvalidCouponCodeException('coupon code ' . $couponCode . ' is not valid');
        }
        $coupon = $this->createCouponEntityFromDatabaseRow($couponRow);
        return $coupon;
    }
    
    
    /**
     * @param array $couponRow
     *
     * @return CouponEntity
     * @throws Exception
     */
    protected function createCouponEntityFromDatabaseRow(array $couponRow): CouponEntity
    {
        /** @var CouponEntity $coupon */
        $coupon = MainFactory::create('CouponEntity');
        $coupon->setCouponId(new IdType((int)$couponRow['coupon_id']));
        $coupon->setCouponType(new NonEmptyStringType($couponRow['coupon_type']));
        $coupon->setCouponCode(new NonEmptyStringType($couponRow['coupon_code']));
        $coupon->setCouponAmount(new DecimalType((float)$couponRow['coupon_amount']));
        $coupon->setCouponMinimumOrder(new DecimalType((float)$couponRow['coupon_minimum_order']));
        $coupon->setCouponStartDate(new DateTime($couponRow['coupon_start_date']));
        $coupon->setCouponExpireDate(new DateTime($couponRow['coupon_expire_date']));
        $coupon->setUsesPerCoupon(new IntType((int)$couponRow['uses_per_coupon']));
        $coupon->setUsesPerUser(new IntType((int)$couponRow['uses_per_user']));
        $coupon->setRestrictToProducts(new StringType((string)$couponRow['restrict_to_products']));
        $coupon->setRestrictToCategories(new StringType((string)$couponRow['restrict_to_categories']));
        $coupon->setRestrictToCustomers(new StringType((string)$couponRow['restrict_to_customers']));
        $coupon->setCouponActive(new NonEmptyStringType($couponRow['coupon_active']));
        $coupon->setDateCreated(new DateTime($couponRow['date_created']));
        $coupon->setDateModified(new DateTime($couponRow['date_modified']));
        return $coupon;
    }
    
    
    /**
     * Stores a coupon in database.
     *
     * If the CouponEntity has a non-empty CouponId, it is assumed to represent an existing record which will be
     * updated, otherwise a new record is created and the CouponId set accordingly.
     *
     * @param CouponEntity $coupon
     */
    public function storeCoupon(CouponEntity $coupon): void
    {
        $dbData = [
            'coupon_type' => $coupon->getCouponType()->asString(),
            'coupon_code' => $coupon->getCouponCode()->asString(),
            'coupon_amount' => $coupon->getCouponAmount()->asDecimal(),
            'coupon_minimum_order' => $coupon->getCouponMinimumOrder()->asDecimal(),
            'coupon_start_date' => $coupon->getCouponStartDate()->format('Y-m-d H:i:s'),
            'coupon_expire_date' => $coupon->getCouponExpireDate()->format('Y-m-d H:i:s'),
            'uses_per_coupon' => $coupon->getUsesPerCoupon()->asInt(),
            'restrict_to_products' => $coupon->getRestrictToProducts()->asString(),
            'restrict_to_categories' => $coupon->getRestrictToCategories()->asString(),
            'restrict_to_customers' => $coupon->getRestrictToCustomers()->asString(),
            'coupon_active' => $coupon->getCouponActive()->asString(),
            'date_created' => $coupon->getDateCreated()->format('Y-m-d H:i:s'),
            'date_modified' => $coupon->getDateModified()->format('Y-m-d H:i:s'),
        ];
        $couponId = $coupon->getCouponId();
        if ($couponId !== null) {
            $this->queryBuilder->update('coupons', $dbData, ['coupon_id' => $couponId->asInt()]);
        } else {
            $this->queryBuilder->insert('coupons', $dbData);
            $couponId = new IdType((int)$this->queryBuilder->insert_id());
            $coupon->setCouponId($couponId);
        }
    }
}
