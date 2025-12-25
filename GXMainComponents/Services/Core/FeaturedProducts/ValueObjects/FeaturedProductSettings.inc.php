<?php
/* --------------------------------------------------------------
   FeaturedProductSettings.inc.php 2019-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FeaturedProductSettings
 */
class FeaturedProductSettings implements FeaturedProductSettingsInterface
{
    /**
     * @var int
     */
    protected $limit;
    
    /**
     * @var bool
     */
    protected $randomOrder;
    
    /**
     * @var int
     */
    protected $customerGroupId;
    
    /**
     * @var bool
     */
    protected $statusFsk18;
    
    /**
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * FeaturedProductSettings constructor.
     *
     * @param IntType      $limit
     * @param BoolType     $randomOrder
     * @param IntType      $customerGroupId
     * @param BoolType     $statusFsk18
     * @param LanguageCode $languageCode
     */
    public function __construct(
        IntType $limit,
        BoolType $randomOrder,
        IntType $customerGroupId,
        BoolType $statusFsk18,
        LanguageCode $languageCode
    ) {
        $this->limit           = $limit->asInt();
        $this->randomOrder     = $randomOrder->asBool();
        $this->customerGroupId = $customerGroupId->asInt();
        $this->statusFsk18     = $statusFsk18->asBool();
        $this->languageCode    = $languageCode->asString();
    }
    
    
    /**
     * Return the limit of products to displayed.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    
    /**
     *checks if random order selected.
     *
     * @return bool
     */
    public function getRandomOrder()
    {
        return $this->randomOrder;
    }
    
    
    /**
     * Return the customer group id.
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }
    
    
    /**
     * Checks if fsk 18 enable or not.
     *
     * @return bool
     */
    public function isFsk18Purchasable()
    {
        return $this->statusFsk18;
    }
    
    
    /**
     * Return the language code.
     *
     * @return string
     */
    public function languageCode()
    {
        return $this->languageCode;
    }
}