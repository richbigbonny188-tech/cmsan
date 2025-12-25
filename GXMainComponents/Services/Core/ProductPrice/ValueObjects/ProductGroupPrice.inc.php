<?php
/* --------------------------------------------------------------
   ProductGroupPrice.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductGroupPrice
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage ValueObjects
 */
class ProductGroupPrice implements ProductGroupPriceInterface
{
    /**
     * @var int
     */
    protected $customerGroupId;
    
    /**
     * @var double|null
     */
    protected $groupPrice;
    
    /**
     * @var \GraduatedPriceCollection|null
     */
    protected $graduatedPrices;
    
    
    /**
     * ProductGroupPrice constructor.
     *
     * @param \IdType                        $customerGroupId Customer group.
     * @param \DecimalType|null              $groupPrice      (Optional) Group price.
     * @param \GraduatedPriceCollection|null $graduatedPrices (Optional) Graduated prices.
     */
    public function __construct(
        IdType $customerGroupId,
        DecimalType $groupPrice = null,
        GraduatedPriceCollection $graduatedPrices = null
    ) {
        $this->customerGroupId = $customerGroupId->asInt();
        $this->groupPrice      = $groupPrice ? $groupPrice->asDecimal() : null;
        $this->graduatedPrices = $graduatedPrices;
    }
    
    
    /**
     * Named constructor of product group price.
     *
     * @param int                            $customerGroupId Customer group.
     * @param double|null                    $groupPrice      (Optional) Group price.
     * @param \GraduatedPriceCollection|null $graduatedPrices (Optional) Graduated prices.
     *
     * @return ProductGroupPrice New instance.
     */
    public static function create(
        $customerGroupId,
        $groupPrice = null,
        GraduatedPriceCollection $graduatedPrices = null
    ) {
        $customerGroupId = new IdType($customerGroupId);
        $groupPrice      = $groupPrice ? new DecimalType($groupPrice) : null;
        
        return MainFactory::create(static::class, $customerGroupId, $groupPrice, $graduatedPrices);
    }
    
    
    /**
     * Returns the customer group id.
     *
     * @return int Customer group id.
     */
    public function customerGroupId()
    {
        return $this->customerGroupId;
    }
    
    
    /**
     * Returns the group price.
     *
     * @return double|null Group price.
     */
    public function groupPrice()
    {
        return $this->groupPrice;
    }
    
    
    /**
     * Returns the graduated prices.
     *
     * @return GraduatedPriceCollection|null
     */
    public function graduatedPrices()
    {
        return $this->graduatedPrices;
    }
}