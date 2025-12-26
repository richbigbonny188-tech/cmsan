<?php
/* --------------------------------------------------------------
   OrderAddressType.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\Model\Export;

/**
 * Class OrderAddressType
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\Model\Export
 */
class OrderAddressType
{
    private const TYPE_CUSTOMER = 'customer';
    
    
    private const TYPE_DELIVERY = 'delivery';
    
    
    private const TYPE_BILLING = 'billing';
    
    
    /**
     * @var string
     */
    private string $type;
    
    
    /**
     * OrderAddressType constructor.
     *
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    
    
    /**
     * Creates a customer address type.
     *
     * @return static
     */
    public static function customerAddress(): self
    {
        return new self(self::TYPE_CUSTOMER);
    }
    
    
    /**
     * Creates a delivery address type.
     *
     * @return static
     */
    public static function deliveryAddress(): self
    {
        return new self(self::TYPE_DELIVERY);
    }
    
    
    /**
     * Creates a billing address type.
     *
     * @return static
     */
    public static function billingAddress(): self
    {
        return new self(self::TYPE_BILLING);
    }
    
    
    /**
     * @return bool
     */
    public function isCustomerAddress(): bool
    {
        return $this->type === self::TYPE_CUSTOMER;
    }
    
    
    /**
     * @return bool
     */
    public function isDeliveryAddress(): bool
    {
        return $this->type === self::TYPE_DELIVERY;
    }
    
    
    /**
     * @return bool
     */
    public function isBillingAddress(): bool
    {
        return $this->type === self::TYPE_BILLING;
    }
}