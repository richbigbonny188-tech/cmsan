<?php

/* --------------------------------------------------------------
   CustomerGroupSettings.inc.php 2017-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupSettings
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage ValueObjects
 */
class CustomerGroupSettings implements CustomerGroupSettingsInterface
{
    /**
     * @var bool
     */
    protected $statusPublic;
    
    /**
     * @var bool
     */
    protected $statusOtDiscount;
    
    /**
     * @var bool
     */
    protected $statusGraduatedPrices;
    
    /**
     * @var bool
     */
    protected $statusShowPrice;
    
    /**
     * @var bool
     */
    protected $statusShowPriceTax;
    
    /**
     * @var bool
     */
    protected $statusAddTaxOt;
    
    /**
     * @var bool
     */
    protected $statusDiscountAttributes;
    
    /**
     * @var bool
     */
    protected $statusFsk18Purchasable;
    
    /**
     * @var bool
     */
    protected $statusFsk18Display;
    
    /**
     * @var bool
     */
    protected $statusWriteReviews;
    
    /**
     * @var bool
     */
    protected $statusReadReviews;
    
    
    public function __construct(
        BoolType $statusPublic,
        BoolType $statusOtDiscount,
        BoolType $statusGraduatedPrices,
        BoolType $statusShowPrice,
        BoolType $statusShowPriceTax,
        BoolType $statusAddTaxOt,
        BoolType $statusDiscountAttributes,
        BoolType $statusFsk18Purchasable,
        BoolType $statusFsk18Display,
        BoolType $statusWriteReviews,
        BoolType $statusReadReviews
    ) {
        $this->statusPublic             = $statusPublic->asBool();
        $this->statusOtDiscount         = $statusOtDiscount->asBool();
        $this->statusGraduatedPrices    = $statusGraduatedPrices->asBool();
        $this->statusShowPrice          = $statusShowPrice->asBool();
        $this->statusShowPriceTax       = $statusShowPriceTax->asBool();
        $this->statusAddTaxOt           = $statusAddTaxOt->asBool();
        $this->statusDiscountAttributes = $statusDiscountAttributes->asBool();
        $this->statusFsk18Purchasable   = $statusFsk18Purchasable->asBool();
        $this->statusFsk18Display       = $statusFsk18Display->asBool();
        $this->statusWriteReviews       = $statusWriteReviews->asBool();
        $this->statusReadReviews        = $statusReadReviews->asBool();
    }
    
    
    /**
     * Checks if the customer group is public or not.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->statusPublic;
    }
    
    
    /**
     * Checks if ot discount flag enable or not.
     *
     * @return bool
     */
    public function isOtDiscountFlag()
    {
        return $this->statusOtDiscount;
    }
    
    
    /**
     * Checks if graduated prices enable or not.
     *
     * @return bool
     */
    public function isGraduatedPrices()
    {
        return $this->statusGraduatedPrices;
    }
    
    
    /**
     * Checks if show price enable or not.
     *
     * @return bool
     */
    public function isShowPrice()
    {
        return $this->statusShowPrice;
    }
    
    
    /**
     * Checks if show price tax enable or not.
     *
     * @return bool
     */
    public function isShowPriceTax()
    {
        return $this->statusShowPriceTax;
    }
    
    
    /**
     * Checks if add tax ot enable or not.
     *
     * @return bool
     */
    public function isAddTaxOt()
    {
        return $this->statusAddTaxOt;
    }
    
    
    /**
     * Checks if discount attributes enable or not.
     *
     * @return bool
     */
    public function isDiscountAttributes()
    {
        return $this->statusDiscountAttributes;
    }
    
    
    /**
     * Checks if fsk 18 enable or not.
     *
     * @return bool
     */
    public function isFsk18Purchasable()
    {
        return $this->statusFsk18Purchasable;
    }
    
    
    /**
     * Checks if fsk 18 display enable or not.
     *
     * @return bool
     */
    public function isFsk18Display()
    {
        return $this->statusFsk18Display;
    }
    
    
    /**
     * Checks if write reviews enable or not.
     *
     * @return bool
     */
    public function isWriteReviews()
    {
        return $this->statusWriteReviews;
    }
    
    
    /**
     * Checks if read reviews enable or not.
     *
     * @return bool
     */
    public function isReadReviews()
    {
        return $this->statusReadReviews;
    }
}