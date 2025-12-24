<?php
/* --------------------------------------------------------------
   CouponEntity.inc.php 2020-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class CouponEntity
{
    /** @var IdType */
    protected $couponId;
    /** @var NonEmptyStringType */
    protected $couponType;
    /** @var NonEmptyStringType */
    protected $couponCode;
    /** @var DecimalType */
    protected $couponAmount;
    /** @var DecimalType */
    protected $couponMinimumOrder;
    /** @var DateTime */
    protected $couponStartDate;
    /** @var DateTime */
    protected $couponExpireDate;
    /** @var IntType */
    protected $usesPerCoupon;
    /** @var IntType */
    protected $usesPerUser;
    /** @var StringType */
    protected $restrictToProducts;
    /** @var StringType */
    protected $restrictToCategories;
    /** @var StringType */
    protected $restrictToCustomers;
    /** @var NonEmptyStringType */
    protected $couponActive;
    /** @var DateTime */
    protected $dateCreated;
    /** @var DateTime */
    protected $dateModified;
    
    
    public function __construct()
    {
        $this->couponId             = null;
        $this->couponType           = new NonEmptyStringType('F');
        $this->couponCode           = new NonEmptyStringType(uniqid('NOCODE', true));
        $this->couponAmount         = new DecimalType(0.0);
        $this->couponMinimumOrder   = new DecimalType(0.0);
        $this->couponStartDate      = new DateTime('1000-01-01 00:00:00');
        $this->couponExpireDate     = new DateTime('1000-01-01 00:00:00');
        $this->usesPerCoupon        = new IntType(1);
        $this->usesPerUser          = new IntType(0);
        $this->restrictToProducts   = new StringType('');
        $this->restrictToCategories = new StringType('');
        $this->restrictToCustomers  = new StringType('');
        $this->couponActive         = new NonEmptyStringType('Y');
        $this->dateCreated          = new DateTime();
        $this->dateModified         = new DateTime();
    }
    
    
    /**
     * @return null|IdType
     */
    public function getCouponId(): ?IdType
    {
        return $this->couponId;
    }
    
    
    /**
     * @param IdType $couponId
     */
    public function setCouponId(IdType $couponId): void
    {
        $this->couponId = $couponId;
    }
    
    
    /**
     * @return NonEmptyStringType
     */
    public function getCouponType(): NonEmptyStringType
    {
        return $this->couponType;
    }
    
    
    /**
     * @param NonEmptyStringType $couponType
     */
    public function setCouponType(NonEmptyStringType $couponType): void
    {
        $this->couponType = $couponType;
    }
    
    
    /**
     * @return NonEmptyStringType
     */
    public function getCouponCode(): NonEmptyStringType
    {
        return $this->couponCode;
    }
    
    
    /**
     * @param NonEmptyStringType $couponCode
     */
    public function setCouponCode(NonEmptyStringType $couponCode): void
    {
        $this->couponCode = $couponCode;
    }
    
    
    /**
     * @return DecimalType
     */
    public function getCouponAmount(): DecimalType
    {
        return $this->couponAmount;
    }
    
    
    /**
     * @param DecimalType $couponAmount
     */
    public function setCouponAmount(DecimalType $couponAmount): void
    {
        $this->couponAmount = $couponAmount;
    }
    
    
    /**
     * @return DateTime
     */
    public function getCouponStartDate(): DateTime
    {
        return $this->couponStartDate;
    }
    
    
    /**
     * @param DateTime $couponStartDate
     */
    public function setCouponStartDate(DateTime $couponStartDate): void
    {
        $this->couponStartDate = $couponStartDate;
    }
    
    
    /**
     * @return DateTime
     */
    public function getCouponExpireDate(): DateTime
    {
        return $this->couponExpireDate;
    }
    
    
    /**
     * @param DateTime $couponExpireDate
     */
    public function setCouponExpireDate(DateTime $couponExpireDate): void
    {
        $this->couponExpireDate = $couponExpireDate;
    }
    
    
    /**
     * @return IntType
     */
    public function getUsesPerCoupon(): IntType
    {
        return $this->usesPerCoupon;
    }
    
    
    /**
     * @param IntType $usesPerCoupon
     */
    public function setUsesPerCoupon(IntType $usesPerCoupon): void
    {
        $this->usesPerCoupon = $usesPerCoupon;
    }
    
    
    /**
     * @return IntType
     */
    public function getUsesPerUser(): IntType
    {
        return $this->usesPerUser;
    }
    
    
    /**
     * @param IntType $usesPerUser
     */
    public function setUsesPerUser(IntType $usesPerUser): void
    {
        $this->usesPerUser = $usesPerUser;
    }
    
    
    /**
     * @return StringType
     */
    public function getRestrictToProducts(): StringType
    {
        return $this->restrictToProducts;
    }
    
    
    /**
     * @param StringType $restrictToProducts
     */
    public function setRestrictToProducts(StringType $restrictToProducts): void
    {
        $this->restrictToProducts = $restrictToProducts;
    }
    
    
    /**
     * @return StringType
     */
    public function getRestrictToCategories(): StringType
    {
        return $this->restrictToCategories;
    }
    
    
    /**
     * @param StringType $restrictToCategories
     */
    public function setRestrictToCategories(StringType $restrictToCategories): void
    {
        $this->restrictToCategories = $restrictToCategories;
    }
    
    
    /**
     * @return StringType
     */
    public function getRestrictToCustomers(): StringType
    {
        return $this->restrictToCustomers;
    }
    
    
    /**
     * @param StringType $restrictToCustomers
     */
    public function setRestrictToCustomers(StringType $restrictToCustomers): void
    {
        $this->restrictToCustomers = $restrictToCustomers;
    }
    
    
    /**
     * @return NonEmptyStringType
     */
    public function getCouponActive(): NonEmptyStringType
    {
        return $this->couponActive;
    }
    
    
    /**
     * @param NonEmptyStringType $couponActive
     */
    public function setCouponActive(NonEmptyStringType $couponActive): void
    {
        $this->couponActive = $couponActive;
    }
    
    
    /**
     * @return DateTime
     */
    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }
    
    
    /**
     * @param DateTime $dateCreated
     */
    public function setDateCreated(DateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }
    
    
    /**
     * @return DateTime
     */
    public function getDateModified(): DateTime
    {
        return $this->dateModified;
    }
    
    
    /**
     * @param DateTime $dateModified
     */
    public function setDateModified(DateTime $dateModified): void
    {
        $this->dateModified = $dateModified;
    }
    
    
    public function toArray(): array
    {
        return [
            'coupon_id'              => $this->couponId->asInt(),
            'coupon_type'            => $this->couponType->asString(),
            'coupon_code'            => $this->couponCode->asString(),
            'coupon_amount'          => $this->couponAmount->asDecimal(),
            'coupon_minimum_order'   => $this->getCouponMinimumOrder()->asDecimal(),
            'coupon_start_date'      => $this->couponStartDate->format('Y-m-d H:i:s'),
            'coupon_expire_date'     => $this->couponExpireDate->format('Y-m-d H:i:s'),
            'uses_per_coupon'        => $this->usesPerCoupon->asInt(),
            'uses_per_user'          => $this->usesPerUser->asInt(),
            'restrict_to_products'   => $this->restrictToProducts->asString(),
            'restrict_to_categories' => $this->restrictToCategories->asString(),
            'restrict_to_customers'  => $this->restrictToCustomers->asString(),
            'coupon_active'          => $this->couponActive->asString(),
            'date_created'           => $this->dateCreated->format('Y-m-d H:i:s'),
            'date_modified'          => $this->dateModified->format('Y-m-d H:i:s'),
        ];
    }
    
    
    /**
     * @return DecimalType
     */
    public function getCouponMinimumOrder(): DecimalType
    {
        return $this->couponMinimumOrder;
    }
    
    
    /**
     * @param DecimalType $couponMinimumOrder
     */
    public function setCouponMinimumOrder(DecimalType $couponMinimumOrder): void
    {
        $this->couponMinimumOrder = $couponMinimumOrder;
    }
    
    
    public function fromArray(array $serialized)
    {
        if (isset($serialized['coupon_id'])) {
            $this->couponId = new IdType((int)$serialized['coupon_id']);
        }
    }
    
    
}
