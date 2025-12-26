<?php
/* --------------------------------------------------------------
   CustomerGroupWriter.inc.php 2019-01-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupWriter
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
class CustomerGroupWriter implements CustomerGroupWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * CustomerGroupWriter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     * @param \LanguageProvider    $languageProvider
     */
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProvider $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Saves customer group entity data in database.
     *
     * @param \CustomerGroupInterface $customerGroup Customer group to be saved.
     *
     * @return $this|\CustomerGroupWriterInterface Same instance for chained method calls.
     */
    public function store(CustomerGroupInterface $customerGroup)
    {
        $lastCustomerGroupId = $this->queryBuilder->select('customers_status_id')
                                   ->from('customers_status')
                                   ->order_by('customers_status_id',
                                              'DESC')
                                   ->get()
                                   ->row_array()['customers_status_id'];
        
        $customerGroupId = (int)$lastCustomerGroupId + 1;
        
        if (count($customerGroup->getNames()) > 0) {
            foreach ($customerGroup->getNames() as $languageCode => $name) {
                $this->queryBuilder->set([
                                             'customers_status_id'                  => $customerGroupId,
                                             'language_id'                          => $this->languageProvider->getIdByCode(MainFactory::create('LanguageCode',
                                                                                                                                                new StringType($languageCode))),
                                             'customers_status_name'                => $name,
                                             'customers_status_public'              => $customerGroup->getSettings()
                                                 ->isPublic(),
                                             'customers_status_min_order'           => $customerGroup->getConfigurations()
                                                 ->getMinOrder(),
                                             'customers_status_max_order'           => $customerGroup->getConfigurations()
                                                 ->getMaxOrder(),
                                             'customers_status_discount'            => $customerGroup->getConfigurations()
                                                 ->getDiscount(),
                                             'customers_status_ot_discount_flag'    => $customerGroup->getSettings()
                                                 ->isOtDiscountFlag(),
                                             'customers_status_ot_discount'         => $customerGroup->getConfigurations()
                                                 ->getOtDiscount(),
                                             'customers_status_graduated_prices'    => $customerGroup->getSettings()
                                                 ->isGraduatedPrices(),
                                             'customers_status_show_price'          => $customerGroup->getSettings()
                                                 ->isShowPrice(),
                                             'customers_status_show_price_tax'      => $customerGroup->getSettings()
                                                 ->isShowPriceTax(),
                                             'customers_status_add_tax_ot'          => $customerGroup->getSettings()
                                                 ->isAddTaxOt(),
                                             'customers_status_payment_unallowed'   => str_replace(' ',
                                                                                                   '',
                                                                                                   implode(',',
                                                                                                           $customerGroup->getConfigurations()
                                                                                                               ->getUnallowedPaymentModules())),
                                             'customers_status_shipping_unallowed'  => str_replace(' ',
                                                                                                   '',
                                                                                                   implode(',',
                                                                                                           $customerGroup->getConfigurations()
                                                                                                               ->getUnallowedShippingModules())),
                                             'customers_status_discount_attributes' => $customerGroup->getSettings()
                                                 ->isDiscountAttributes(),
                                             'customers_fsk18_purchasable'          => $customerGroup->getSettings()
                                                 ->isFsk18Purchasable(),
                                             'customers_fsk18_display'              => $customerGroup->getSettings()
                                                 ->isFsk18Display(),
                                             'customers_status_write_reviews'       => $customerGroup->getSettings()
                                                 ->isWriteReviews(),
                                             'customers_status_read_reviews'        => $customerGroup->getSettings()
                                                 ->isReadReviews()
                                         ]);
                
                $this->queryBuilder->insert('customers_status');
                $this->_setDefault($customerGroup, $customerGroupId);
            }
        }
        
        $customerGroup->setId(new IntType($customerGroupId));
        $this->_createPersonalOfferTable($customerGroupId)
            ->_createGroupPermissionColumn($customerGroupId, 'categories')
            ->_createGroupPermissionColumn($customerGroupId, 'products');
        
        return $this;
    }
    
    
    /**
     * Updates customer group entity data in database.
     *
     * @param \CustomerGroupInterface $customerGroup Customer group to be  updated.
     *
     * @return $this|\CustomerGroupWriterInterface Same instance for chained method calls.
     */
    public function update(CustomerGroupInterface $customerGroup)
    {
        foreach ($customerGroup->getNames() as $languageCode => $name) {
            $languageId = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
            
            $this->queryBuilder->update('customers_status',
                                        [
                                            'customers_status_name'                => $name,
                                            'customers_status_public'              => $customerGroup->getSettings()
                                                ->isPublic(),
                                            'customers_status_min_order'           => $customerGroup->getConfigurations()
                                                ->getMinOrder(),
                                            'customers_status_max_order'           => $customerGroup->getConfigurations()
                                                ->getMaxOrder(),
                                            'customers_status_discount'            => $customerGroup->getConfigurations()
                                                ->getDiscount(),
                                            'customers_status_ot_discount_flag'    => $customerGroup->getSettings()
                                                ->isOtDiscountFlag(),
                                            'customers_status_ot_discount'         => $customerGroup->getConfigurations()
                                                ->getOtDiscount(),
                                            'customers_status_graduated_prices'    => $customerGroup->getSettings()
                                                ->isGraduatedPrices(),
                                            'customers_status_show_price'          => $customerGroup->getSettings()
                                                ->isShowPrice(),
                                            'customers_status_show_price_tax'      => $customerGroup->getSettings()
                                                ->isShowPriceTax(),
                                            'customers_status_add_tax_ot'          => $customerGroup->getSettings()
                                                ->isAddTaxOt(),
                                            'customers_status_payment_unallowed'   => str_replace(' ',
                                                                                                  '',
                                                                                                  implode(',',
                                                                                                          $customerGroup->getConfigurations()
                                                                                                              ->getUnallowedPaymentModules())),
                                            'customers_status_shipping_unallowed'  => str_replace(' ',
                                                                                                  '',
                                                                                                  implode(',',
                                                                                                          $customerGroup->getConfigurations()
                                                                                                              ->getUnallowedShippingModules())),
                                            'customers_status_discount_attributes' => $customerGroup->getSettings()
                                                ->isDiscountAttributes(),
                                            'customers_fsk18_purchasable'          => $customerGroup->getSettings()
                                                ->isFsk18Purchasable(),
                                            'customers_fsk18_display'              => $customerGroup->getSettings()
                                                ->isFsk18Display(),
                                            'customers_status_write_reviews'       => $customerGroup->getSettings()
                                                ->isWriteReviews(),
                                            'customers_status_read_reviews'        => $customerGroup->getSettings()
                                                ->isReadReviews()
                                        ],
                                        [
                                            'customers_status_id' => $customerGroup->getId(),
                                            'language_id'         => $languageId
                                        ]);
        }
        $this->_setDefault($customerGroup, $customerGroup->getId());
        
        return $this;
    }
    
    
    /**
     * Create base data from chosen personal offers table.
     *
     * @param \IntType $customerGroupId
     * @param \IntType $baseId
     *
     * @return \CustomerGroupWriter
     */
    public function createBase(IntType $customerGroupId, IntType $baseId)
    {
        if ($customerGroupId->asInt() !== $baseId->asInt()) {
            $this->queryBuilder->query('INSERT INTO `personal_offers_by_customers_status_' . $customerGroupId->asInt() . '` (`price_id`, `products_id`, `quantity`,`personal_offer`)
		                            SELECT `price_id`, `products_id`, `quantity`,`personal_offer` FROM `personal_offers_by_customers_status_'
                                       . $baseId->asInt() . '`');
        }
        
        return $this;
    }
    
    
    /**
     * Updates the default customer group in the configuration data table.
     *
     * @param \CustomerGroupInterface $customerGroup
     * @param                         $customerGroupId
     *
     * @return \CustomerGroupWriter
     */
    protected function _setDefault(CustomerGroupInterface $customerGroup, $customerGroupId)
    {
        if ($customerGroup->isDefault()) {
            $this->queryBuilder->update('gx_configurations',
                                        ['value' => $customerGroupId],
                                        ['key' => 'configuration/DEFAULT_CUSTOMERS_STATUS_ID']);
        }
        
        return $this;
    }
    
    
    /**
     * Creates an personal offer table with given id as suffix.
     *
     * @param $customerGroupId
     *
     * @return $this
     */
    protected function _createPersonalOfferTable($customerGroupId)
    {
        $this->_dropPersonalOfferTableIfExists($customerGroupId)
            ->_addGroupPermissionToProducts($customerGroupId)
            ->_addGroupPermissionToCategories($customerGroupId)->queryBuilder->query('CREATE TABLE `personal_offers_by_customers_status_'
                                                                                     . $customerGroupId . '` (
                                                                                     `price_id` INT(11) NOT NULL AUTO_INCREMENT,
                                                                                     `products_id` INT(11) NOT NULL DEFAULT \'0\',
                                                                                     `quantity` DECIMAL(15,4) DEFAULT NULL,
                                                                                     `personal_offer` DECIMAL(15,4) DEFAULT NULL,
                                                                                     PRIMARY KEY (`price_id`),
                                                                                     UNIQUE KEY `unique_offer` (`products_id`,`quantity`)
                                                                                     ) ENGINE=InnoDB DEFAULT CHARSET=`utf8`');
        
        return $this;
    }
    
    
    /**
     * Drops personal offer table, if table with id exist.
     *
     * @param $customerGroupId
     *
     * @return $this
     */
    protected function _dropPersonalOfferTableIfExists($customerGroupId)
    {
        $this->queryBuilder->query('DROP TABLE IF EXISTS personal_offers_by_customers_status_' . $customerGroupId);
        
        return $this;
    }
    
    
    /**
     * Add group permission id to products table.
     *
     * @param $customerGroupId
     *
     * @return $this
     */
    protected function _addGroupPermissionToProducts($customerGroupId)
    {
        if ($this->_columnNotExists('group_permission_' . $customerGroupId, 'products')) {
            $this->queryBuilder->query('ALTER TABLE  `products` ADD  `group_permission_' . $customerGroupId
                                       . '` TINYINT( 1 ) NOT NULL DEFAULT 0');
        }
        
        return $this;
    }
    
    
    /**
     * Add group permission id to categories table.
     *
     * @param $customerGroupId
     *
     * @return $this
     */
    protected function _addGroupPermissionToCategories($customerGroupId)
    {
        if ($this->_columnNotExists('group_permission_' . $customerGroupId, 'categories')) {
            $this->queryBuilder->query('ALTER TABLE  `categories` ADD  `group_permission_' . $customerGroupId
                                       . '` TINYINT( 1 ) NOT NULL DEFAULT 0');
        }
        
        return $this;
    }
    
    
    /**
     * Check if given column exists in given table.
     *
     * @param $column string
     *
     * @param $table  string
     *
     * @return bool
     */
    protected function _columnNotExists($column, $table)
    {
        $tableColumns = $this->queryBuilder->query('SHOW COLUMNS IN ' . $table . ' LIKE "' . addslashes($column) . '"')
            ->result_array();
        
        return count($tableColumns) < 1;
    }
    
    
    /**
     * Creates an group_permission by id column in given table.
     *
     * @param $id    int
     *
     * @param $table string
     *
     * @return $this
     */
    protected function _createGroupPermissionColumn($id, $table)
    {
        if ($this->_columnNotExists('group_permission_' . $id, $table)) {
            $this->queryBuilder->query('ALTER TABLE  `' . $table . '` ADD  `group_permission_' . $id
                                       . '` TINYINT( 1 ) NOT NULL DEFAULT 0');
        }
        
        return $this;
    }
}