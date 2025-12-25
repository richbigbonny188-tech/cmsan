<?php
/* --------------------------------------------------------------
   CustomerGroupFactory.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupFactory
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Factories
 */
class CustomerGroupFactory
{
    /**
     * @var \CustomerGroupRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * CustomerGroupFactory constructor.
     *
     * @param \CustomerGroupRepositoryInterface $repository
     */
    public function __construct(CustomerGroupRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns new instances of customer group entities.
     *
     * @return \CustomerGroup
     */
    public function createEntity()
    {
        $false          = new BoolType(false);
        $defaultDecimal = new DecimalType(00.00);
        
        return MainFactory::create('CustomerGroup',
                                   $this->repository,
                                   new EditableKeyValueCollection([]),
                                   $this->createConfigurations($defaultDecimal, $defaultDecimal),
                                   $this->createSettings($false,
                                                         $false,
                                                         $false,
                                                         $false,
                                                         $false,
                                                         $false,
                                                         $false,
                                                         $false,
                                                         $false,
                                                         $false,
                                                         $false));
    }
    
    
    /**
     * Returns a new instance of customer group settings.
     *
     * @param \BoolType $statusPublic
     * @param \BoolType $statusOtDiscount
     * @param \BoolType $statusGraduatedPrices
     * @param \BoolType $statusShowPrice
     * @param \BoolType $statusShowPriceTax
     * @param \BoolType $statusAddTaxOt
     * @param \BoolType $statusDiscountAttributes
     * @param \BoolType $statusFsk18Purchasable
     * @param \BoolType $statusFsk18Display
     * @param \BoolType $statusWriteReviews
     * @param \BoolType $statusReadReviews
     *
     * @return CustomerGroupSettings
     */
    public function createSettings(
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
        
        return MainFactory::create('CustomerGroupSettings',
                                   $statusPublic,
                                   $statusOtDiscount,
                                   $statusGraduatedPrices,
                                   $statusShowPrice,
                                   $statusShowPriceTax,
                                   $statusAddTaxOt,
                                   $statusDiscountAttributes,
                                   $statusFsk18Purchasable,
                                   $statusFsk18Display,
                                   $statusWriteReviews,
                                   $statusReadReviews);
    }
    
    
    /**
     * Returns a new instance of customer group configuration.
     *
     * @param \DecimalType      $discount
     * @param \DecimalType      $otDiscount
     * @param \DecimalType|null $minOrder
     * @param \DecimalType|null $maxOrder
     * @param array             $unallowedPaymentModules
     * @param array             $unallowedShippingModules
     *
     * @return CustomerGroupConfigurations
     */
    public function createConfigurations(
        DecimalType $discount,
        DecimalType $otDiscount,
        DecimalType $minOrder = null,
        DecimalType $maxOrder = null,
        array $unallowedPaymentModules = [],
        array $unallowedShippingModules = []
    ) {
        return MainFactory::create('CustomerGroupConfigurations',
                                   $discount,
                                   $otDiscount,
                                   $minOrder,
                                   $maxOrder,
                                   $unallowedPaymentModules,
                                   $unallowedShippingModules);
    }
    
    
    /**
     * Returns new instances of customer group collections.
     *
     * @return \CustomerGroupCollection
     */
    public function createCollection()
    {
        return MainFactory::create('CustomerGroupCollection');
    }
}